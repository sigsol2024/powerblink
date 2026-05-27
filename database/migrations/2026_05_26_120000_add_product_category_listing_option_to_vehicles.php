<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 7 (additive). Adds the product_category dimension that replaces the legacy
 * make/model/body_type filter set:
 *   1. Adds `product_category_listing_option_id` (nullable, indexed) to vehicles.
 *   2. Adds the `product_category` category row to listing_option_categories.
 *   3. Seeds a starter set of apparel categories.
 *
 * Reversible: the down() drops the column, the category row, and its child options.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicles') && ! Schema::hasColumn('vehicles', 'product_category_listing_option_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->unsignedBigInteger('product_category_listing_option_id')->nullable()->after('stock');
                $table->index('product_category_listing_option_id', 'vehicles_product_category_idx');
            });
        }

        if (Schema::hasTable('listing_option_categories')) {
            $now = now();
            $exists = DB::table('listing_option_categories')->where('slug', 'product_category')->exists();
            if (! $exists) {
                DB::table('listing_option_categories')->insert([
                    'slug' => 'product_category',
                    'label' => 'Product category',
                    'sort_order' => 5,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $categoryId = (int) DB::table('listing_option_categories')->where('slug', 'product_category')->value('id');
            if ($categoryId > 0 && Schema::hasTable('listing_options')) {
                $existingCount = (int) DB::table('listing_options')->where('category_id', $categoryId)->count();
                if ($existingCount === 0) {
                    $seedCategories = [
                        ['value' => 'Dress', 'sort_order' => 10],
                        ['value' => 'Coat', 'sort_order' => 20],
                        ['value' => 'Blazer', 'sort_order' => 30],
                        ['value' => 'Shirt', 'sort_order' => 40],
                        ['value' => 'Top', 'sort_order' => 50],
                        ['value' => 'Trouser', 'sort_order' => 60],
                        ['value' => 'Bag', 'sort_order' => 70],
                        ['value' => 'Footwear', 'sort_order' => 80],
                        ['value' => 'Accessory', 'sort_order' => 90],
                    ];
                    foreach ($seedCategories as $row) {
                        DB::table('listing_options')->insert([
                            'category_id' => $categoryId,
                            'parent_id' => null,
                            'value' => $row['value'],
                            'sort_order' => $row['sort_order'],
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vehicles') && Schema::hasColumn('vehicles', 'product_category_listing_option_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropIndex('vehicles_product_category_idx');
                $table->dropColumn('product_category_listing_option_id');
            });
        }

        if (Schema::hasTable('listing_option_categories')) {
            $categoryId = (int) DB::table('listing_option_categories')->where('slug', 'product_category')->value('id');
            if ($categoryId > 0 && Schema::hasTable('listing_options')) {
                DB::table('listing_options')->where('category_id', $categoryId)->delete();
            }
            DB::table('listing_option_categories')->where('slug', 'product_category')->delete();
        }
    }
};
