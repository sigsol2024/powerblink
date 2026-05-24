<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('order_items') || ! Schema::hasTable('vehicle_variants')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('vehicle_variant_id')
                ->references('id')
                ->on('vehicle_variants')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('order_items')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['vehicle_variant_id']);
        });
    }
};
