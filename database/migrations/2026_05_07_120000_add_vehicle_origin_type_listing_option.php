<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('listing_option_categories') || ! Schema::hasTable('listing_options')) {
            return;
        }

        $now = now();
        $exists = DB::table('listing_option_categories')->where('slug', 'vehicle_origin_type')->exists();
        if (! $exists) {
            DB::table('listing_option_categories')->insert([
                'slug' => 'vehicle_origin_type',
                'label' => 'Type',
                'sort_order' => 75,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $catId = (int) DB::table('listing_option_categories')->where('slug', 'vehicle_origin_type')->value('id');
        if ($catId <= 0) {
            return;
        }

        foreach ([['Nigerian', 1], ['Foreign', 2]] as [$label, $order]) {
            $has = DB::table('listing_options')
                ->where('category_id', $catId)
                ->whereNull('parent_id')
                ->whereRaw('LOWER(TRIM(value)) = ?', [mb_strtolower($label)])
                ->exists();
            if (! $has) {
                DB::table('listing_options')->insert([
                    'category_id' => $catId,
                    'parent_id' => null,
                    'value' => $label,
                    'sort_order' => $order,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if (! Schema::hasTable('vehicles')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'type_listing_option_id')) {
                $table->foreignId('type_listing_option_id')
                    ->nullable()
                    ->after('country_listing_option_id')
                    ->constrained('listing_options')
                    ->nullOnDelete();
            }
        });

        $nigerianId = (int) DB::table('listing_options')
            ->where('category_id', $catId)
            ->whereNull('parent_id')
            ->whereRaw('LOWER(TRIM(value)) = ?', ['nigerian'])
            ->value('id');

        $foreignId = (int) DB::table('listing_options')
            ->where('category_id', $catId)
            ->whereNull('parent_id')
            ->whereRaw('LOWER(TRIM(value)) = ?', ['foreign'])
            ->value('id');

        $countryCatId = (int) DB::table('listing_option_categories')->where('slug', 'country')->value('id');
        $nigeriaId = $countryCatId > 0
            ? (int) DB::table('listing_options')
                ->where('category_id', $countryCatId)
                ->whereNull('parent_id')
                ->whereRaw('LOWER(TRIM(value)) = ?', ['nigeria'])
                ->value('id')
            : 0;

        if ($nigerianId > 0 && $nigeriaId > 0) {
            DB::table('vehicles')
                ->whereNull('type_listing_option_id')
                ->where('country_listing_option_id', $nigeriaId)
                ->update(['type_listing_option_id' => $nigerianId]);
        }

        if ($foreignId > 0) {
            DB::table('vehicles')
                ->whereNull('type_listing_option_id')
                ->update(['type_listing_option_id' => $foreignId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vehicles') && Schema::hasColumn('vehicles', 'type_listing_option_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropConstrainedForeignId('type_listing_option_id');
            });
        }

        if (! Schema::hasTable('listing_option_categories')) {
            return;
        }

        $catId = (int) DB::table('listing_option_categories')->where('slug', 'vehicle_origin_type')->value('id');
        if ($catId > 0) {
            DB::table('listing_options')->where('category_id', $catId)->delete();
            DB::table('listing_option_categories')->where('id', $catId)->delete();
        }
    }
};
