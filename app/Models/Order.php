<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'subtotal',
        'shipping',
        'tax',
        'total',
        'currency',
        'status',
        'stock_deducted_at',
        'customer_notified_at',
        'admin_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'shipping' => 'integer',
            'tax' => 'integer',
            'total' => 'integer',
            'stock_deducted_at' => 'datetime',
            'customer_notified_at' => 'datetime',
            'admin_notified_at' => 'datetime',
        ];
    }

    public static function generateOrderNumber(): string
    {
        return 'VD-'.strtoupper(Str::random(8));
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
