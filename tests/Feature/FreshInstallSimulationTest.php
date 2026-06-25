<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FreshInstallSimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrate_after_fresh_seed_has_no_pending_migrations(): void
    {
        $this->seed(DatabaseSeeder::class);

        $countBefore = \Illuminate\Support\Facades\DB::table('migrations')->count();

        Artisan::call('migrate', ['--force' => true]);

        $countAfter = \Illuminate\Support\Facades\DB::table('migrations')->count();
        $this->assertSame($countBefore, $countAfter);
    }

    public function test_seeded_traffic_events_do_not_use_legacy_vogue_domain(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(
            0,
            DB::table('site_traffic_events')->where('url', 'like', '%voguedress%')->count()
        );
    }
}
