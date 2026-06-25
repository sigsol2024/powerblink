<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoLoginSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_bootstrap_admin_can_access_admin_dashboard(): void
    {
        $user = User::query()->where('email', config('powerblink.bootstrap_admin_email'))->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isSuperAdmin());

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_coach_parent_and_player_demo_logins_work(): void
    {
        $password = (string) config('powerblink.demo_user_password');
        $this->assertNotSame('', $password);

        foreach ([
            'coach@powerblinkfc.com' => 'coach',
            'parent@powerblinkfc.com' => 'parent',
            'player@powerblinkfc.com' => 'player',
        ] as $email => $role) {
            $user = User::query()->where('email', $email)->first();
            $this->assertNotNull($user, "Missing demo user: {$email}");
            $this->assertTrue(Hash::check($password, (string) $user->password));
            $this->assertTrue($user->hasRole($role));

            $this->actingAs($user)
                ->get(route('portal.dashboard'))
                ->assertOk();
        }
    }
}
