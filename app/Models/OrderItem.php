<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'vehicle_id',
        'vehicle_variant_id',
        'sku',
        'name',
        'unit_price',
        'qty',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'integer',
            'qty' => 'integer',
            'line_total' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(VehicleVariant::class, 'vehicle_variant_id');
    }
}
