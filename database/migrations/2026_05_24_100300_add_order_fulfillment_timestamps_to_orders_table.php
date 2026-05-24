<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'stock_deducted_at')) {
                $table->timestamp('stock_deducted_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('orders', 'customer_notified_at')) {
                $table->timestamp('customer_notified_at')->nullable()->after('stock_deducted_at');
            }
            if (! Schema::hasColumn('orders', 'admin_notified_at')) {
                $table->timestamp('admin_notified_at')->nullable()->after('customer_notified_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('orders', 'admin_notified_at') ? 'admin_notified_at' : null,
                Schema::hasColumn('orders', 'customer_notified_at') ? 'customer_notified_at' : null,
                Schema::hasColumn('orders', 'stock_deducted_at') ? 'stock_deducted_at' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
