<?php

namespace Tests\Feature;

use App\Models\AdminAuditTrail;
use App\Models\User;
use Database\Seeders\AcademyPermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
        $this->seed(AcademyPermissionsSeeder::class);
    }

    private function makeAdmin(array $attrs = [], bool $super = false): User
    {
        $user = User::factory()->create($attrs);
        $user->assignRole('admin');
        if ($super) {
            $user->forceFill(['is_super_admin' => true])->save();
        }

        return $user;
    }

    public function test_admin_can_access_dashboard_and_registrations(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.registrations.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.media.index'))->assertOk();
    }

    public function test_coach_cannot_access_admin_panel(): void
    {
        $coach = User::factory()->create(['email_verified_at' => now()]);
        $coach->assignRole('coach');

        $this->actingAs($coach)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($coach)->get(route('admin.settings.edit'))->assertForbidden();
    }

    public function test_coach_can_access_portal(): void
    {
        $coach = User::factory()->create(['email_verified_at' => now()]);
        $coach->assignRole('coach');

        $this->actingAs($coach)->get(route('portal.dashboard'))->assertOk();
    }

    public function test_admin_can_delete_another_admin_but_not_super_admin(): void
    {
        $super = $this->makeAdmin(['email' => 'super@example.com'], true);
        $admin = $this->makeAdmin(['email' => 'admin2@example.com']);
        $other = $this->makeAdmin(['email' => 'admin3@example.com']);

        $this->actingAs($admin)->delete(route('admin.staff.destroy', $other))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $other->id]);

        $this->actingAs($admin)->delete(route('admin.staff.destroy', $super))->assertForbidden();
    }

    public function test_login_redirects_differ_by_role(): void
    {
        $admin = $this->makeAdmin();
        $coach = User::factory()->create(['email_verified_at' => now()]);
        $coach->assignRole('coach');

        $this->assertSame(route('admin.dashboard', absolute: false), $admin->staffHomeRoute());
        $this->assertSame(route('portal.dashboard', absolute: false), $coach->staffHomeRoute());
        $this->assertSame(route('portal.dashboard', absolute: false), $coach->loginRedirectPath());
    }

    public function test_admin_can_set_staff_password_on_create_and_edit(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.staff.store'), [
            'name' => 'Admin Two',
            'email' => 'admin2@example.com',
            'password' => 'Str0ng!Pass99',
            'password_confirmation' => 'Str0ng!Pass99',
            'role' => 'admin',
        ])->assertRedirect();

        $created = User::query()->where('email', 'admin2@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('Str0ng!Pass99', $created->password));

        $this->actingAs($admin)->put(route('admin.staff.update', $created), [
            'name' => 'Admin Two',
            'email' => 'admin2@example.com',
            'role' => 'admin',
            'password' => 'NewStr0ng!Pass88',
            'password_confirmation' => 'NewStr0ng!Pass88',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('NewStr0ng!Pass88', $created->fresh()->password));
    }

    public function test_staff_create_writes_structured_audit_log_without_password(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.staff.store'), [
            'name' => 'Audit Admin',
            'email' => 'auditadmin@example.com',
            'password' => 'Str0ng!Pass99',
            'password_confirmation' => 'Str0ng!Pass99',
            'role' => 'admin',
        ])->assertRedirect();

        $entry = AdminAuditTrail::query()
            ->where('meta->action', 'staff.created')
            ->latest('id')
            ->first();
        $this->assertNotNull($entry);
        $this->assertSame('staff.created', $entry->meta['action'] ?? null);
        $this->assertArrayNotHasKey('password', $entry->meta ?? []);
    }

    public function test_super_admin_can_be_updated_without_submitting_role_field(): void
    {
        $super = $this->makeAdmin(['name' => 'Super User', 'email' => 'super@example.com'], true);

        $this->actingAs($super)->put(route('admin.staff.update', $super), [
            'name' => 'Super User Updated',
            'email' => 'super@example.com',
        ])->assertRedirect();

        $this->assertSame('Super User Updated', $super->fresh()->name);
        $this->assertTrue($super->fresh()->isSuperAdmin());
        $this->assertTrue($super->fresh()->hasRole('admin'));
    }

    public function test_cannot_demote_last_admin_when_no_super_admin_exists(): void
    {
        $admin = $this->makeAdmin(['email' => 'only-admin@example.com']);

        $this->actingAs($admin)->put(route('admin.staff.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'coach',
        ])->assertRedirect()->assertSessionHasErrors('role');
    }
}
