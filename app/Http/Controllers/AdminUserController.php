<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('roles')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => User::query()->count(),
            'admins' => User::query()->role('admin')->count(),
            'customers' => User::query()->whereDoesntHave('roles', fn ($q) => $q->where('name', 'admin'))->count(),
        ];

        return view('admin.users.index', [
            'users' => $users,
            'userStats' => $stats,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin,user'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $user->assignRole($data['role']);

        return back();
    }

    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting yourself accidentally in this simple UI.
        abort_if(Auth::id() === $user->id, 400);

        DB::transaction(function () use ($user): void {
            DB::table('sessions')->where('user_id', $user->id)->delete();
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            $user->syncRoles([]);
            $user->delete();
        });

        return back();
    }
}

