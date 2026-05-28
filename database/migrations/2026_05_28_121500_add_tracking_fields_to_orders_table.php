<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number', 64)->nullable()->after('delivery_status');
            }
            if (! Schema::hasColumn('orders', 'tracking_url')) {
                $table->string('tracking_url', 2048)->nullable()->after('tracking_number');
            }
            if (! Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('tracking_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivered_at')) {
                $table->dropColumn('delivered_at');
            }
            if (Schema::hasColumn('orders', 'tracking_url')) {
                $table->dropColumn('tracking_url');
            }
            if (Schema::hasColumn('orders', 'tracking_number')) {
                $table->dropColumn('tracking_number');
            }
        });
    }
};

