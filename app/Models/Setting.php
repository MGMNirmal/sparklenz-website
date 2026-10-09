<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'group', 'label', 'type', 'value', 'help', 'sort_order'])]
class Setting extends Model
{
    public const CACHE_KEY = 'site-settings';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * All settings as a key → value map, cached until the next change.
     *
     * @return array<string, string|null>
     */
    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): array => static::query()->pluck('value', 'key')->all());
    }
}
