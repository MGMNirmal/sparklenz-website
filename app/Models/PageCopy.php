<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['page', 'key', 'label', 'type', 'content', 'max_length', 'sort_order'])]
class PageCopy extends Model
{
    public const CACHE_KEY = 'page-copy';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * All copy as "page.key" → content, cached until the next change.
     *
     * @return array<string, string|null>
     */
    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): array => static::query()->get()
            ->mapWithKeys(fn (PageCopy $copy): array => [$copy->page.'.'.$copy->key => $copy->content])
            ->all());
    }
}
