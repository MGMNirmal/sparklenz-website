<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[Fillable([
    'slug', 'name', 'summary', 'intro', 'price_prefix', 'starting_price', 'starting_unit',
    'starting_label', 'includes', 'notes', 'is_published', 'sort_order',
])]
class Service extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'includes' => 'array',
            'notes' => 'array',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return HasMany<ServiceRate, $this>
     */
    public function rates(): HasMany
    {
        return $this->hasMany(ServiceRate::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @param  Builder<Service>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true)->orderBy('sort_order');
    }

    /**
     * Rate rows grouped by their table heading, preserving order.
     *
     * @return Collection<string, Collection<int, ServiceRate>>
     */
    public function rateGroups(): Collection
    {
        return $this->rates->groupBy(fn (ServiceRate $rate): string => (string) $rate->group_label);
    }
}
