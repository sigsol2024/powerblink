<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedInteger('finance_min_down_payment')->nullable()->after('finance_down_payment');
            $table->unsignedSmallInteger('finance_term_min_months')->nullable()->after('finance_min_down_payment');
            $table->unsignedSmallInteger('finance_term_max_months')->nullable()->after('finance_term_min_months');
        });

        // Data safety layer:
        // - If price missing or 0: financing cannot be computed reliably → disable calculator
        // - Clamp min down payment to not exceed price
        // - Ensure max term >= min term when both provided
        DB::table('vehicles')
            ->where(function ($q) {
                $q->whereNull('price')->orWhere('price', '<=', 0);
            })
            ->update(['show_financing_calculator' => false]);

        // Avoid SQL LEAST(): some SQLite builds used in CI/dev omit scalar LEAST().
        DB::statement(
            'UPDATE vehicles SET finance_min_down_payment = CASE '
                .'WHEN COALESCE(finance_min_down_payment, finance_down_payment, 0) <= COALESCE(price, 0) '
                .'THEN COALESCE(finance_min_down_payment, finance_down_payment, 0) '
                .'ELSE COALESCE(price, 0) END '
                .'WHERE COALESCE(price, 0) > 0'
        );
        DB::statement('UPDATE vehicles SET finance_term_max_months = finance_term_min_months WHERE finance_term_min_months IS NOT NULL AND finance_term_max_months IS NOT NULL AND finance_term_max_months < finance_term_min_months');
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'finance_min_down_payment',
                'finance_term_min_months',
                'finance_term_max_months',
            ]);
        });
    }
};
