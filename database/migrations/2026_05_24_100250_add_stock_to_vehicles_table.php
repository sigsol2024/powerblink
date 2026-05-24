<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vehicles')) {
            return;
        }

        if (Schema::hasColumn('vehicles', 'stock')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->after('price');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('vehicles') || ! Schema::hasColumn('vehicles', 'stock')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
