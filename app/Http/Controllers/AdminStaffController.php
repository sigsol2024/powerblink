<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Admin\AdminAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    public function index(): View
    {
        $this->authorize('manageStaff', User::class);

        $staff = User::query()
            ->role('admin')
            ->with('roles')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => User::query()->role('admin')->count(),
            'admins' => User::query()->role('admin')->count(),
        ];

        return view('admin.staff.index', [
            'staff' => $staff,
            'staffStats' => $stats,
        ]);
    }

    public function store(Request $request, AdminAuditLogger $audit): RedirectResponse
    {
        $this->authorize('manageStaff', User::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'email_verified_at' => now(),
        ]);

        $user->assignRole($data['role']);

        $audit->logStaffCreated($request, $user, $data['role']);

        return back()->with('status', __('Staff account created.'));
    }

    public function update(Request $request, User $user, AdminAuditLogger $audit): RedirectResponse
    {
        $this->authorize('updateStaff', $user);

        if ($user->isSuperAdmin()) {
            $request->merge(['role' => 'admin']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin'],
        ]);

        if ($user->isSuperAdmin() && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => __('The super admin account must remain an admin.')]);
        }

        if ($user->hasRole('admin') && $data['role'] !== 'admin') {
            $this->ensureAdminLockoutPrevented($user);
        }

        $changedFields = [];
        if ($user->name !== $data['name']) {
            $changedFields[] = 'name';
        }
        if ($user->email !== $data['email']) {
            $changedFields[] = 'email';
        }
        if (! $user->hasRole($data['role'])) {
            $changedFields[] = 'role';
        }

        $passwordChanged = ! empty($data['password'] ?? null);

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if ($passwordChanged) {
            $user->password = $data['password'];
            $changedFields[] = 'password';
        }

        $user->save();
        $user->syncRoles([$data['role']]);

        $audit->logStaffUpdated($request, $user, $changedFields, $passwordChanged);

        return back()->with('status', __('Staff account updated.'));
    }

    public function destroy(Request $request, User $user, AdminAuditLogger $audit): RedirectResponse
    {
        $this->authorize('deleteStaff', $user);

        $this->ensureAdminLockoutPrevented($user);

        $snapshot = clone $user;

        DB::transaction(function () use ($user): void {
            DB::table('sessions')->where('user_id', $user->id)->delete();
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            $user->syncRoles([]);
            $user->delete();
        });

        $audit->logStaffDeleted($request, $snapshot);

        return back()->with('status', __('Staff account deleted.'));
    }

    private function ensureAdminLockoutPrevented(User $user): void
    {
        if (! $user->hasRole('admin')) {
            return;
        }

        $remainingAdmins = User::query()
            ->role('admin')
            ->where('id', '!=', $user->id)
            ->count();

        $hasSuperAdmin = User::query()
            ->where('is_super_admin', true)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($remainingAdmins === 0 && ! $hasSuperAdmin) {
            abort(400, __('Cannot delete the last admin account.'));
        }
    }
}
