<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32)->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 40)->nullable();
            $table->string('shipping_address_line1');
            $table->string('shipping_address_line2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_state')->nullable();
            $table->string('shipping_postal_code', 20)->nullable();
            $table->string('shipping_country', 2)->default('NG');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('shipping')->default(0);
            $table->unsignedBigInteger('tax')->default(0);
            $table->unsignedBigInteger('total');
            $table->string('currency', 3)->default('NGN');
            $table->string('status', 32)->default('pending_payment');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('vehicle_variant_id')->nullable();
            $table->string('sku', 64)->nullable();
            $table->string('name');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedSmallInteger('qty');
            $table->unsignedBigInteger('line_total');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 32)->default('paystack');
            $table->string('reference')->unique();
            $table->string('status', 32)->default('pending');
            $table->unsignedBigInteger('amount');
            $table->string('currency', 3)->default('NGN');
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
