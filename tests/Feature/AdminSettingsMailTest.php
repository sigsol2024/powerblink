<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Mail\OutboundMailService;
use Database\Seeders\AcademyPermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsMailTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $this->seed(RolesSeeder::class);
        $this->seed(AcademyPermissionsSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    public function test_guest_cannot_send_settings_mail_test(): void
    {
        $this->post(route('admin.settings.mail-test'), [
            'test_email' => 'test@example.com',
        ])->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_send_settings_mail_test(): void
    {
        $this->seed(RolesSeeder::class);
        $this->seed(AcademyPermissionsSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('parent');

        $this->actingAs($user)
            ->post(route('admin.settings.mail-test'), ['test_email' => 'test@example.com'])
            ->assertForbidden();
    }

    public function test_admin_mail_test_requires_email(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->from(route('admin.settings.edit'))
            ->post(route('admin.settings.mail-test'), [])
            ->assertSessionHasErrors('test_email');
    }

    public function test_admin_mail_test_succeeds_when_mailer_send_works(): void
    {
        $this->mock(OutboundMailService::class, function ($mock) {
            $mock->shouldReceive('send')->once();
        });

        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->from(route('admin.settings.edit'))
            ->post(route('admin.settings.mail-test'), ['test_email' => 'admin-mail-test@example.com'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('mail_test_status');
    }
}
