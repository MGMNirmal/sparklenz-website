<?php

namespace App\Models;

use App\Enums\MediaType;
use App\Enums\ProjectStatus;
use App\Support\YouTube;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title', 'slug', 'client_label', 'category', 'summary', 'description', 'media_type',
    'cover_path', 'cover_alt', 'cover_width', 'cover_height', 'youtube_id', 'video_orientation',
    'teaser_path', 'is_featured', 'is_demo', 'sort_order', 'status', 'published_at',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'media_type' => MediaType::class,
            'status' => ProjectStatus::class,
            'is_featured' => 'boolean',
            'is_demo' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return HasMany<ProjectMedia, $this>
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProjectMedia::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Published records only, and concept/demo records only where the
     * environment allows them.
     *
     * @param  Builder<Project>  $query
     */
    #[Scope]
    protected function visible(Builder $query): void
    {
        $query->where('status', ProjectStatus::Published);

        if (! config('sparklenz.show_demo_content')) {
            $query->where('is_demo', false);
        }
    }

    /**
     * @param  Builder<Project>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function coverUrl(): ?string
    {
        if ($this->cover_path) {
            return Storage::disk('public')->url($this->cover_path);
        }

        return $this->youtube_id ? YouTube::posterUrl($this->youtube_id) : null;
    }

    /**
     * URL of the smaller rendition generated at upload time, if present.
     */
    public function coverThumbUrl(): ?string
    {
        if ($this->cover_path) {
            $thumb = preg_replace('/\.webp$/', '-sm.webp', $this->cover_path);

            if ($thumb !== $this->cover_path && Storage::disk('public')->exists($thumb)) {
                return Storage::disk('public')->url($thumb);
            }
        }

        return $this->coverUrl();
    }

    public function teaserUrl(): ?string
    {
        return $this->teaser_path ? Storage::disk('public')->url($this->teaser_path) : null;
    }

    public function hasFilm(): bool
    {
        return YouTube::isValidId($this->youtube_id);
    }

    public function isPortrait(): bool
    {
        return $this->video_orientation === 'portrait';
    }

    /**
     * Data passed to the screening-room dialog. All values are plain
     * strings; the front end inserts them as text, never as HTML.
     *
     * @return array<string, mixed>
     */
    public function toScreeningPayload(): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'client' => $this->client_label,
            'category' => $this->category,
            'summary' => $this->summary,
            'description' => $this->description,
            'type' => $this->media_type->value,
            'youtubeId' => $this->hasFilm() ? $this->youtube_id : null,
            'portrait' => $this->isPortrait(),
            'isDemo' => $this->is_demo,
            'cover' => $this->coverUrl(),
            'coverAlt' => $this->cover_alt,
            'images' => $this->media->map(fn (ProjectMedia $item): array => [
                'src' => $item->url(),
                'alt' => $item->alt,
                'caption' => $item->caption,
                'width' => $item->width,
                'height' => $item->height,
            ])->values()->all(),
        ];
    }
}
