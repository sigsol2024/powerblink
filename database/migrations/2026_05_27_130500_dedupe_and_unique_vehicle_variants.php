<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dedupe existing duplicates so the unique index can be created safely.
        // We keep the lowest id and delete the rest for each (vehicle, size, color) tuple.
        $groups = DB::table('vehicle_variants')
            ->select([
                'vehicle_id',
                'size_listing_option_id',
                'color_listing_option_id',
                DB::raw('MIN(id) as keep_id'),
                DB::raw('COUNT(*) as total'),
            ])
            ->groupBy('vehicle_id', 'size_listing_option_id', 'color_listing_option_id')
            ->having('total', '>', 1)
            ->get();

        foreach ($groups as $g) {
            $q = DB::table('vehicle_variants')
                ->where('vehicle_id', $g->vehicle_id)
                ->where('id', '!=', $g->keep_id);

            if ($g->size_listing_option_id === null) {
                $q->whereNull('size_listing_option_id');
            } else {
                $q->where('size_listing_option_id', $g->size_listing_option_id);
            }

            if ($g->color_listing_option_id === null) {
                $q->whereNull('color_listing_option_id');
            } else {
                $q->where('color_listing_option_id', $g->color_listing_option_id);
            }

            $q->delete();
        }

        Schema::table('vehicle_variants', function (Blueprint $table) {
            $table->unique(
                ['vehicle_id', 'size_listing_option_id', 'color_listing_option_id'],
                'vehicle_variants_vehicle_size_color_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_variants', function (Blueprint $table) {
            $table->dropUnique('vehicle_variants_vehicle_size_color_unique');
        });
    }
};

