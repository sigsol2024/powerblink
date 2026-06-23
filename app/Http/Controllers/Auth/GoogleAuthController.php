<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Throwable;

class GoogleAuthController extends Controller
{
    protected function googleOAuthConfigured(): bool
    {
        $id = config('services.google.client_id');
        $secret = config('services.google.client_secret');
        $redirect = config('services.google.redirect');

        return is_string($id) && trim($id) !== ''
            && is_string($secret) && trim($secret) !== ''
            && is_string($redirect) && trim($redirect) !== '';
    }

    public function redirect(Request $request): RedirectResponse
    {
        if (! $this->googleOAuthConfigured()) {
            return redirect()->route('login')->withErrors(['email' => __('Google sign-in is not configured.')]);
        }

        $intent = $request->query('intent', 'login');
        if (! in_array($intent, ['login', 'register'], true)) {
            $intent = 'login';
        }
        $request->session()->put('google_oauth_intent', $intent);

        /** @var \Laravel\Socialite\Two\AbstractProvider $provider */
        $provider = Socialite::driver('google');

        $hint = trim((string) $request->query('login_hint', ''));
        if ($hint !== '') {
            return $provider->with(['login_hint' => $hint])->redirect();
        }

        return $provider->redirect();
    }

    public function callback(Request $request, EmailOtpService $otp): RedirectResponse
    {
        if (! $this->googleOAuthConfigured()) {
            return redirect()->route('login')->withErrors(['email' => __('Google sign-in is not configured.')]);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors(['email' => __('Google sign-in was cancelled or failed.')]);
        }

        $email = $googleUser->getEmail();
        if (! is_string($email) || trim($email) === '') {
            return redirect()->route('login')->withErrors(['email' => __('Google did not return an email address.')]);
        }

        $googleId = (string) $googleUser->getId();
        $name = (string) ($googleUser->getName() ?: Str::before($email, '@'));
        $avatar = $googleUser->getAvatar();

        $intent = (string) $request->session()->pull('google_oauth_intent', 'login');
        if (! in_array($intent, ['login', 'register'], true)) {
            $intent = 'login';
        }

        $userByGoogle = User::query()->where('google_id', $googleId)->first();
        if ($userByGoogle) {
            $user = $userByGoogle;
            if (is_string($avatar) && $avatar !== '' && $user->avatar !== $avatar) {
                $user->forceFill(['avatar' => $avatar])->save();
            }

            return $this->finalizeGoogleLogin($otp, $user);
        }

        $existingByEmail = User::query()->where('email', $email)->first();

        if ($intent === 'register') {
            if ($existingByEmail) {
                return redirect()->route('login', ['tab' => 'login'])
                    ->withErrors(['email' => __('An account with this email already exists. Sign in with Google from the Sign in tab instead.')]);
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Str::password(32),
                'google_id' => $googleId,
                'avatar' => is_string($avatar) ? $avatar : null,
                'email_verified_at' => now(),
            ]);
            Role::findOrCreate('user');
            $user->assignRole('user');

            $request->session()->put('google_welcome_pending', [
                'name' => $user->name,
                'email' => $user->email,
            ]);
            return $this->finalizeGoogleLogin($otp, $user);
        }

        // intent === login
        if (! $existingByEmail) {
            return redirect()->route('login', ['tab' => 'register'])
                ->withErrors(['email' => __('No account exists for this Google email yet. Create an account on the Create account tab, then you can use Google.')]);
        }

        if ($existingByEmail->google_id !== null && $existingByEmail->google_id !== $googleId) {
            return redirect()->route('login')->withErrors(['email' => __('This email is already linked to another sign-in method.')]);
        }

        $existingByEmail->forceFill([
            'google_id' => $googleId,
            'email_verified_at' => $existingByEmail->email_verified_at ?? now(),
        ]);
        if (is_string($avatar) && $avatar !== '') {
            $existingByEmail->avatar = $avatar;
        }
        $existingByEmail->save();

        return $this->finalizeGoogleLogin($otp, $existingByEmail);
    }

    protected function finalizeGoogleLogin(EmailOtpService $otp, User $user): RedirectResponse
    {
        if ($user->email_login_otp_enabled) {
            request()->session()->put('login_otp_user_id', $user->id);
            request()->session()->put('login_otp_remember', true);
            try {
                if (! $otp->issueForLogin($user)) {
                    request()->session()->forget(['login_otp_user_id', 'login_otp_remember']);

                    return redirect()->route('login')->withErrors(['email' => __('Could not send login code. Try again shortly.')]);
                }
            } catch (Throwable $e) {
                report($e);
                request()->session()->forget(['login_otp_user_id', 'login_otp_remember']);

                return redirect()->route('login')->withErrors(['email' => __('Could not send login code. Check mail configuration.')]);
            }

            return redirect()->route('login.otp.show');
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        if (request()->session()->has('google_welcome_pending')) {
            return redirect()
                ->route('auth.google.welcome')
                ->cookie('mt_google_email', $user->email, 60 * 24 * 365, null, null, false, true, false, 'Lax');
        }

        return redirect()
            ->to($user->loginRedirectPath())
            ->cookie('mt_google_email', $user->email, 60 * 24 * 365, null, null, false, true, false, 'Lax');
    }

    public function welcome(Request $request)
    {
        $payload = $request->session()->pull('google_welcome_pending');
        if (! is_array($payload)) {
            return redirect()->route('dashboard');
        }

        return view('auth.google-welcome', [
            'name' => (string) ($payload['name'] ?? ''),
            'email' => (string) ($payload['email'] ?? ''),
        ]);
    }
}
