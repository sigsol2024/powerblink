<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_traffic_events', function (Blueprint $table) {
            $table->id();
            $table->string('path', 1024);
            $table->string('route_name')->nullable();
            $table->text('url')->nullable();
            $table->string('method', 10)->default('GET');
            $table->string('referrer_host')->nullable();
            $table->text('referrer_url')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('session_id', 120)->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('vehicle_slug')->nullable();
            $table->timestamp('viewed_at')->index();
            $table->timestamps();

            $table->index(['viewed_at', 'route_name']);
            $table->index(['viewed_at', 'vehicle_id']);
            $table->index(['viewed_at', 'session_id']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE `site_traffic_events` ADD INDEX `site_traffic_events_viewed_at_path_index` (`viewed_at`, `path`(150))');
        } else {
            Schema::table('site_traffic_events', function (Blueprint $table) {
                $table->index(['viewed_at', 'path']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_traffic_events');
    }
};
