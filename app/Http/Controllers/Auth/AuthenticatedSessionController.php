<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * @deprecated Login GET is served by UnifiedAuthController; kept for compatibility if referenced.
     */
    public function create(): View
    {
        return view('auth.unified');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, EmailOtpService $otp): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        if ($user->email_login_otp_enabled) {
            $remember = $request->boolean('remember');
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->regenerate();
            $request->session()->put('login_otp_user_id', $user->id);
            $request->session()->put('login_otp_remember', $remember);

            try {
                if (! $otp->issueForLogin($user)) {
                    $request->session()->forget(['login_otp_user_id', 'login_otp_remember']);

                    return redirect()->route('login')->withErrors(['email' => __('Could not send login code. Try again shortly.')]);
                }
            } catch (\Throwable $e) {
                report($e);
                $request->session()->forget(['login_otp_user_id', 'login_otp_remember']);

                return redirect()->route('login')->withErrors(['email' => __('Could not send login code. Check mail configuration.')]);
            }

            return redirect()->route('login.otp.show');
        }

        $request->session()->regenerate();

        return redirect()->to($user->loginRedirectPath());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $wasStaff = $request->user()?->isStaff() ?? false;

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $wasStaff
            ? redirect()->route('login')
            : redirect('/');
    }
}
