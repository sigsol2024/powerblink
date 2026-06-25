<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_traffic_events')) {
            return;
        }

        Schema::table('site_traffic_events', function (Blueprint $table): void {
            if (Schema::hasColumn('site_traffic_events', 'vehicle_id')) {
                $table->dropIndex('site_traffic_events_viewed_at_vehicle_id_index');
                $table->dropForeign(['vehicle_id']);
            }
        });

        Schema::table('site_traffic_events', function (Blueprint $table): void {
            if (Schema::hasColumn('site_traffic_events', 'vehicle_id')) {
                $table->dropColumn('vehicle_id');
            }

            if (Schema::hasColumn('site_traffic_events', 'vehicle_slug')) {
                $table->dropColumn('vehicle_slug');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('site_traffic_events')) {
            return;
        }

        Schema::table('site_traffic_events', function (Blueprint $table): void {
            if (! Schema::hasColumn('site_traffic_events', 'vehicle_id')) {
                $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
                $table->index(['viewed_at', 'vehicle_id']);
            }

            if (! Schema::hasColumn('site_traffic_events', 'vehicle_slug')) {
                $table->string('vehicle_slug')->nullable();
            }
        });
    }
};
