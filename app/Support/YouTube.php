<?php

namespace App\Support;

/**
 * Extracts and validates YouTube video IDs from the URL formats the
 * studio is expected to paste. Only YouTube hosts are accepted and no
 * remote request is ever made.
 */
class YouTube
{
    private const ID_PATTERN = '/^[A-Za-z0-9_-]{11}$/';

    private const HOSTS = [
        'youtube.com',
        'www.youtube.com',
        'm.youtube.com',
        'music.youtube.com',
        'youtube-nocookie.com',
        'www.youtube-nocookie.com',
        'youtu.be',
        'www.youtu.be',
    ];

    public static function isValidId(?string $id): bool
    {
        return is_string($id) && preg_match(self::ID_PATTERN, $id) === 1;
    }

    /**
     * Returns the 11-character video ID, or null when the input is not a
     * recognised YouTube watch, youtu.be, Shorts, embed or live link.
     */
    public static function extractId(?string $input): ?string
    {
        $input = trim((string) $input);

        if ($input === '') {
            return null;
        }

        if (self::isValidId($input)) {
            return $input;
        }

        if (! preg_match('#^https?://#i', $input)) {
            $input = 'https://'.$input;
        }

        $parts = parse_url($input);

        if ($parts === false || empty($parts['host'])) {
            return null;
        }

        $host = strtolower($parts['host']);

        if (! in_array($host, self::HOSTS, true)) {
            return null;
        }

        $path = $parts['path'] ?? '';
        $candidate = null;

        if (str_ends_with($host, 'youtu.be')) {
            $candidate = explode('/', ltrim($path, '/'))[0] ?? null;
        } elseif (preg_match('#^/(shorts|embed|live|v)/([^/?]+)#', $path, $matches)) {
            $candidate = $matches[2];
        } elseif ($path === '/watch' || $path === '/watch/') {
            parse_str($parts['query'] ?? '', $query);
            $candidate = is_string($query['v'] ?? null) ? $query['v'] : null;
        }

        return self::isValidId($candidate) ? $candidate : null;
    }

    public static function watchUrl(string $id): string
    {
        return 'https://www.youtube.com/watch?v='.$id;
    }

    public static function posterUrl(string $id): string
    {
        return 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg';
    }
}
