<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAccessSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_order_show_requires_valid_signature(): void
    {
        $order = Order::query()->create([
            'order_number' => 'VD-TEST0001',
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'shipping_address_line1' => '1 Test St',
            'shipping_city' => 'Lagos',
            'shipping_country' => 'NG',
            'subtotal' => 10000,
            'shipping' => 0,
            'tax' => 0,
            'total' => 10000,
            'currency' => 'NGN',
            'status' => 'paid',
        ]);

        $this->get(route('order.show', $order))->assertForbidden();

        $signed = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'order.show',
            now()->addHour(),
            ['order' => $order->id]
        );

        $this->get($signed)->assertOk();
    }
}
