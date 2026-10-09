<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'service_id', 'group_label', 'label', 'detail', 'price_prefix', 'price',
    'previous_price', 'unit', 'sort_order',
])]
class ServiceRate extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'previous_price' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function formattedPrice(): string
    {
        if ($this->price === null) {
            return 'On request';
        }

        return trim(($this->price_prefix ? $this->price_prefix.' ' : '').Money::lkr($this->price));
    }
}
