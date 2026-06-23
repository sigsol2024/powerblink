<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginOtpChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('login_otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.login-otp');
    }

    public function store(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $userId = (int) $request->session()->get('login_otp_user_id', 0);
        if ($userId <= 0) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        if (! $otp->verifyLogin($userId, $request->string('code')->toString())) {
            return back()->withErrors(['code' => __('Invalid or expired code.')]);
        }

        $request->session()->forget('login_otp_user_id');
        $user = User::query()->findOrFail($userId);

        Auth::login($user, (bool) $request->session()->pull('login_otp_remember', false));
        $request->session()->regenerate();

        $home = $user->staffHomeRoute();

        return redirect()->intended($home);
    }

    public function resend(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $userId = (int) $request->session()->get('login_otp_user_id', 0);
        if ($userId <= 0) {
            return redirect()->route('login');
        }

        $user = User::query()->find($userId);
        if (! $user) {
            return redirect()->route('login');
        }

        if ($otp->secondsUntilSendAllowed(EmailOtpService::PURPOSE_LOGIN, (string) $userId) > 0) {
            return back()->withErrors(['code' => __('Please wait before requesting another code.')]);
        }

        try {
            if (! $otp->issueForLogin($user)) {
                return back()->withErrors(['code' => __('Could not resend code yet.')]);
            }
        } catch (\Throwable) {
            return back()->withErrors(['code' => __('Could not send email. Check mail configuration.')]);
        }

        return back()->with('status', __('A new code has been sent to your email.'));
    }

    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget(['login_otp_user_id', 'login_otp_remember']);

        return redirect()->route('login');
    }
}
