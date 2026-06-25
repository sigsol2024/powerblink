<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('vehicle_favorites');
        Schema::dropIfExists('vehicle_inquiries');
        Schema::dropIfExists('vehicle_images');
        Schema::dropIfExists('vehicle_variants');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vendor_profiles');
        Schema::dropIfExists('listing_options');
        Schema::dropIfExists('listing_option_categories');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Ecommerce tables are removed intentionally; restore from earlier migrations if needed.
    }
};
