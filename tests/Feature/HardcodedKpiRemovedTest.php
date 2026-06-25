<?php

namespace Tests\Feature;

use Database\Seeders\CmsPagesSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HardcodedKpiRemovedTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_does_not_render_stitch_fallback_player_count(): void
    {
        $this->seed([RolesSeeder::class, CmsPagesSeeder::class]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('600+', false);
    }
}
