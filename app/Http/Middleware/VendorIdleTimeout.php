<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs out non-admin users after a period of inactivity (separate from global session lifetime).
 */
class VendorIdleTimeout
{
    public const SESSION_KEY = 'vendor_last_activity_at';

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || $user->isStaff()) {
            return $next($request);
        }

        $minutes = max(1, (int) config('session.vendor_idle_timeout', 15));
        $threshold = $minutes * 60;
        $now = time();
        $last = $request->session()->get(self::SESSION_KEY);

        if (is_int($last) && ($now - $last) > $threshold) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('status', __('Your session expired due to inactivity. Please sign in again.'));
        }

        $request->session()->put(self::SESSION_KEY, $now);

        return $next($request);
    }
}
