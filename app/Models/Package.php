<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug', 'name', 'tagline', 'type_label', 'description', 'billing_unit', 'tier_column_label',
    'price_column_label', 'alt_price_column_label', 'additional_item_price', 'additional_item_unit',
    'includes', 'notes', 'is_featured', 'is_published', 'sort_order',
])]
class Package extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'includes' => 'array',
            'notes' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'additional_item_price' => 'integer',
        ];
    }

    /**
     * @return HasMany<PackageTier, $this>
     */
    public function tiers(): HasMany
    {
        return $this->hasMany(PackageTier::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @param  Builder<Package>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true)->orderBy('sort_order');
    }

    /**
     * The lowest current price across all tiers and columns.
     */
    public function startingPrice(): ?int
    {
        $prices = $this->tiers->flatMap(fn (PackageTier $tier): array => array_filter([$tier->price, $tier->alt_price]));

        return $prices->isEmpty() ? null : (int) $prices->min();
    }
}
