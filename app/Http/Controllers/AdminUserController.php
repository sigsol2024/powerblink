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

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->role('user')
            ->with('roles')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => User::query()->role('user')->count(),
        ];

        return view('admin.users.index', [
            'title' => __('Customers'),
            'users' => $users,
            'userStats' => $stats,
            'canManageCustomers' => request()->user()?->can('customers.manage') ?? false,
        ]);
    }

    public function store(Request $request, AdminAuditLogger $audit): RedirectResponse
    {
        $this->authorize('manageCustomers', User::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'email_verified_at' => now(),
        ]);

        $user->assignRole('user');

        $audit->logCustomerCreated($request, $user);

        return back()->with('status', __('Customer account created.'));
    }

    public function update(Request $request, User $user, AdminAuditLogger $audit): RedirectResponse
    {
        $this->authorize('updateCustomer', $user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $changedFields = [];
        if ($user->name !== $data['name']) {
            $changedFields[] = 'name';
        }
        if ($user->email !== $data['email']) {
            $changedFields[] = 'email';
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

        $audit->logCustomerUpdated($request, $user, $changedFields, $passwordChanged);

        return back()->with('status', __('Customer account updated.'));
    }

    public function destroy(Request $request, User $user, AdminAuditLogger $audit): RedirectResponse
    {
        $this->authorize('deleteCustomer', $user);

        $snapshot = clone $user;

        DB::transaction(function () use ($user): void {
            DB::table('sessions')->where('user_id', $user->id)->delete();
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            $user->syncRoles([]);
            $user->delete();
        });

        $audit->logCustomerDeleted($request, $snapshot);

        return back()->with('status', __('Customer account deleted.'));
    }
}
