<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Stores uploaded media on the public disk under generated names.
 *
 * Images are decoded and re-encoded as WebP with GD, which discards any
 * embedded payloads and metadata, then resized to sensible web sizes.
 * A small rendition is written alongside for cards and thumbnails.
 */
class MediaStorage
{
    public const LARGE_EDGE = 2000;

    public const SMALL_EDGE = 900;

    private const DISK = 'public';

    /**
     * @return array{path: string, width: int, height: int}
     */
    public function storeImage(UploadedFile|string $source, string $directory): array
    {
        $sourcePath = $source instanceof UploadedFile ? $source->getRealPath() : $source;
        $info = @getimagesize($sourcePath);

        if ($info === false || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            throw new RuntimeException('The file is not a supported image.');
        }

        $image = @imagecreatefromstring((string) file_get_contents($sourcePath));

        if ($image === false) {
            throw new RuntimeException('The image could not be decoded.');
        }

        $image = $this->applyExifOrientation($image, $sourcePath, $info[2]);

        $name = trim($directory, '/').'/'.Str::uuid()->toString();
        $large = $this->resize($image, self::LARGE_EDGE);
        $small = $this->resize($image, self::SMALL_EDGE);

        $this->writeWebp($large, $name.'.webp');
        $this->writeWebp($small, $name.'-sm.webp');

        $width = imagesx($large);
        $height = imagesy($large);

        imagedestroy($image);

        return ['path' => $name.'.webp', 'width' => $width, 'height' => $height];
    }

    public function storeTeaser(UploadedFile $file): string
    {
        $extension = match ($file->getMimeType()) {
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            default => throw new RuntimeException('Teasers must be MP4 or WebM video.'),
        };

        return $file->storeAs('teasers', Str::uuid()->toString().'.'.$extension, self::DISK);
    }

    public function storeTeaserFromPath(string $path): string
    {
        $name = 'teasers/'.Str::uuid()->toString().'.'.strtolower(pathinfo($path, PATHINFO_EXTENSION));
        Storage::disk(self::DISK)->put($name, (string) file_get_contents($path));

        return $name;
    }

    /**
     * Deletes stored media, including the small rendition of an image.
     */
    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        $paths = [$path];

        if (str_ends_with($path, '.webp')) {
            $paths[] = preg_replace('/\.webp$/', '-sm.webp', $path);
        }

        Storage::disk(self::DISK)->delete($paths);
    }

    private function resize(\GdImage $image, int $maxEdge): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, $maxEdge / max($width, $height));

        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        return $canvas;
    }

    private function writeWebp(\GdImage $image, string $path): void
    {
        ob_start();
        imagewebp($image, null, 82);
        $contents = (string) ob_get_clean();
        imagedestroy($image);

        Storage::disk(self::DISK)->put($path, $contents);
    }

    private function applyExifOrientation(\GdImage $image, string $path, int $type): \GdImage
    {
        if ($type !== IMAGETYPE_JPEG || ! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($path)['Orientation'] ?? 1);

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        return $rotated === false ? $image : $rotated;
    }
}
