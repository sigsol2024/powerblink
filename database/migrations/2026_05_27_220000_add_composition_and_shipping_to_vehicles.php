<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'composition_care')) {
                $table->text('composition_care')->nullable()->after('overview');
            }
            if (! Schema::hasColumn('vehicles', 'shipping_returns')) {
                $table->text('shipping_returns')->nullable()->after('composition_care');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'shipping_returns')) {
                $table->dropColumn('shipping_returns');
            }
            if (Schema::hasColumn('vehicles', 'composition_care')) {
                $table->dropColumn('composition_care');
            }
        });
    }
};
