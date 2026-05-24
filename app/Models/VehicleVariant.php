<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleVariant extends Model
{
    protected $fillable = [
        'vehicle_id',
        'sku',
        'size_listing_option_id',
        'color_listing_option_id',
        'price',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function sizeOption(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'size_listing_option_id');
    }

    public function colorOption(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'color_listing_option_id');
    }

    public function unitPriceNaira(Vehicle $vehicle): int
    {
        $price = $this->price ?? $vehicle->price;

        return max(0, (int) $price);
    }

    public function displayLabel(): string
    {
        $parts = array_filter([
            $this->sizeOption?->value,
            $this->colorOption?->value,
        ]);

        return $parts !== [] ? implode(' / ', $parts) : __('Standard');
    }
}
