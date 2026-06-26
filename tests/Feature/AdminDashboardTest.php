<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AcademyPermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
        $this->seed(AcademyPermissionsSeeder::class);
    }

    public function test_admin_dashboard_returns_kpi_data(): void
    {
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('stats', function (array $stats): bool {
            return array_key_exists('active_players', $stats)
                && array_key_exists('active_registrations', $stats)
                && array_key_exists('monthly_revenue', $stats)
                && array_key_exists('attendance_rate', $stats);
        });
        $response->assertViewHas('upcomingEvents');
        $response->assertViewHas('pendingPayments');
        $response->assertViewHas('performanceTrends');
    }
}
