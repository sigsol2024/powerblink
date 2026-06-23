<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * One-time first admin creation (same idea as Prestige CREATE_ADMIN_TEMP.php).
 * Enable with ADMIN_BOOTSTRAP_ENABLED=true in .env, create admin, then disable and deploy without this route.
 */
class TemporaryAdminController extends Controller
{
    public function create(): View
    {
        $this->ensureBootstrapAllowed();

        if ($this->adminExists()) {
            $admins = User::role('admin')->orderBy('id')->get(['id', 'name', 'email', 'created_at']);

            return view('auth.bootstrap-admin', [
                'blocked' => true,
                'admins' => $admins,
                'error' => null,
                'success' => null,
            ]);
        }

        return view('auth.bootstrap-admin', [
            'blocked' => false,
            'admins' => collect(),
            'error' => null,
            'success' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureBootstrapAllowed();

        if ($this->adminExists()) {
            return redirect()->route('bootstrap.admin');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');
        $user->forceFill(['is_super_admin' => true])->save();

        Auth::login($user);

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Admin account created. Set ADMIN_BOOTSTRAP_ENABLED=false in .env and remove bootstrap routes.');
    }

    private function ensureBootstrapAllowed(): void
    {
        if (! config('app.admin_bootstrap_enabled')) {
            abort(404);
        }
    }

    private function adminExists(): bool
    {
        return User::role('admin')->exists();
    }
}
