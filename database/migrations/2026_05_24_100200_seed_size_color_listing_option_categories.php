<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('listing_option_categories')) {
            return;
        }

        $now = now();
        foreach ([
            ['slug' => 'size', 'label' => 'Size', 'sort_order' => 50],
            ['slug' => 'color', 'label' => 'Color', 'sort_order' => 51],
        ] as $row) {
            $exists = DB::table('listing_option_categories')->where('slug', $row['slug'])->exists();
            if (! $exists) {
                DB::table('listing_option_categories')->insert([
                    'slug' => $row['slug'],
                    'label' => $row['label'],
                    'sort_order' => $row['sort_order'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('listing_option_categories')) {
            return;
        }

        DB::table('listing_option_categories')->whereIn('slug', ['size', 'color'])->delete();
    }
};
