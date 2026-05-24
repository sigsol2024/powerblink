<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('sku', 64)->nullable();
            $table->foreignId('size_listing_option_id')->nullable()->constrained('listing_options')->nullOnDelete();
            $table->foreignId('color_listing_option_id')->nullable()->constrained('listing_options')->nullOnDelete();
            $table->unsignedInteger('price')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['vehicle_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_variants');
    }
};
