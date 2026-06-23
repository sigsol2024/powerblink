<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\EmailOtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisterOtpController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('pending_registration')) {
            return redirect()->route('login');
        }

        return view('auth.register-verify');
    }

    public function store(Request $request, EmailOtpService $otp): RedirectResponse
    {
        if (! $request->session()->has('pending_registration')) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $token = (string) $request->session()->get('pending_registration_otp_token', '');
        if ($token === '' || ! $otp->verifyRegistration($token, $request->string('code')->toString())) {
            return back()->withErrors(['code' => __('Invalid or expired code.')]);
        }

        $data = $request->session()->get('pending_registration', []);
        $request->session()->forget(['pending_registration', 'pending_registration_otp_token']);

        $user = User::create([
            'name' => (string) ($data['name'] ?? ''),
            'email' => (string) ($data['email'] ?? ''),
            'password' => (string) ($data['password'] ?? ''),
            'email_verified_at' => now(),
        ]);

        Role::findOrCreate('user');
        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        return redirect()
            ->route('dashboard')
            ->with('status', __('Welcome! Your account is ready.'));
    }

    public function resend(Request $request, EmailOtpService $otp): RedirectResponse
    {
        if (! $request->session()->has('pending_registration')) {
            return redirect()->route('login');
        }

        $data = $request->session()->get('pending_registration', []);
        $token = (string) $request->session()->get('pending_registration_otp_token', '');
        if ($token === '') {
            return redirect()->route('register.verify.show');
        }

        $email = (string) ($data['email'] ?? '');
        $name = (string) ($data['name'] ?? '');
        if ($email === '') {
            return redirect()->route('login');
        }

        if ($otp->secondsUntilSendAllowed(EmailOtpService::PURPOSE_REGISTRATION, $token) > 0) {
            return back()->withErrors(['code' => __('Please wait before requesting another code.')]);
        }

        try {
            if (! $otp->issueForRegistration($token, $email, $name)) {
                return back()->withErrors(['code' => __('Could not resend code yet. Try again shortly.')]);
            }
        } catch (\Throwable) {
            return back()->withErrors(['code' => __('Could not send email. Check mail configuration.')]);
        }

        return back()->with('status', __('A new code has been sent to your email.'));
    }
}
