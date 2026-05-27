<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Mail\OutboundMailService;
use App\Support\SiteBrand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Throwable;

/**
 * Email OTP for registration verification and optional login second step.
 */
class EmailOtpService
{
    public const PURPOSE_REGISTRATION = 'registration';

    public const PURPOSE_LOGIN = 'login';

    public const PURPOSE_OTP_SETTINGS_ENABLE = 'otp_settings_enable';

    private const CACHE_TTL_SECONDS = 600;

    public const SEND_WINDOW_SECONDS = 60;

    private const VERIFY_MAX_ATTEMPTS = 5;

    private const VERIFY_DECAY_SECONDS = 60;

    public function __construct(
        private readonly OutboundMailService $mailer,
    ) {}

    public static function codeValidMinutes(): int
    {
        return max(1, (int) round(self::CACHE_TTL_SECONDS / 60));
    }

    public function cacheHashKey(string $purpose, string $identifier): string
    {
        $safePurpose = preg_replace('/[^a-z0-9_-]/i', '', $purpose) ?: 'otp';

        return 'email_otp_hash:'.$safePurpose.':'.hash('sha256', $identifier);
    }

    private function sendRateLimitKey(string $purpose, string $identifier): string
    {
        return 'email-otp-send:'.$purpose.':'.hash('sha256', $identifier);
    }

    private function verifyRateLimitKey(string $purpose, string $identifier): string
    {
        return 'email-otp-verify:'.$purpose.':'.hash('sha256', $identifier);
    }

    /**
     * @return bool True if code was generated and mail accepted by transport chain.
     */
    public function issue(string $purpose, string $identifier, string $toEmail, string $toName, string $subjectLine): bool
    {
        $sendKey = $this->sendRateLimitKey($purpose, $identifier);
        if (RateLimiter::tooManyAttempts($sendKey, 1)) {
            return false;
        }

        $code = str_pad((string) random_int(0, 999_999), 6, '0', STR_PAD_LEFT);
        $hash = hash('sha256', $code.config('app.key'));
        Cache::put($this->cacheHashKey($purpose, $identifier), $hash, self::CACHE_TTL_SECONDS);

        $html = View::make('emails.otp', [
            'purpose' => $purpose,
            'code' => $code,
            'expiresMinutes' => self::codeValidMinutes(),
            'subjectLine' => $subjectLine,
        ])->render();

        try {
            $this->mailer->send($toEmail, $toName, $subjectLine, $html, $toEmail, $toName);
        } catch (Throwable $e) {
            Cache::forget($this->cacheHashKey($purpose, $identifier));
            throw $e;
        }

        RateLimiter::hit($sendKey, self::SEND_WINDOW_SECONDS);

        return true;
    }

    public function verify(string $purpose, string $identifier, string $code): bool
    {
        $verifyKey = $this->verifyRateLimitKey($purpose, $identifier);
        if (RateLimiter::tooManyAttempts($verifyKey, self::VERIFY_MAX_ATTEMPTS)) {
            return false;
        }

        $cacheKey = $this->cacheHashKey($purpose, $identifier);
        $expected = Cache::get($cacheKey);
        if (! is_string($expected) || $expected === '') {
            RateLimiter::hit($verifyKey, self::VERIFY_DECAY_SECONDS);

            return false;
        }

        $guess = hash('sha256', $code.config('app.key'));
        if (! hash_equals($expected, $guess)) {
            RateLimiter::hit($verifyKey, self::VERIFY_DECAY_SECONDS);

            return false;
        }

        Cache::forget($cacheKey);
        RateLimiter::clear($verifyKey);

        return true;
    }

    public function secondsUntilSendAllowed(string $purpose, string $identifier): int
    {
        return RateLimiter::availableIn($this->sendRateLimitKey($purpose, $identifier));
    }

    public function forget(string $purpose, string $identifier): void
    {
        Cache::forget($this->cacheHashKey($purpose, $identifier));
    }

    public function issueForLogin(User $user): bool
    {
        $subject = __('Your :app sign-in code', ['app' => SiteBrand::displayName()]);

        return $this->issue(self::PURPOSE_LOGIN, (string) $user->id, (string) $user->email, (string) $user->name, $subject);
    }

    public function verifyLogin(int $userId, string $code): bool
    {
        return $this->verify(self::PURPOSE_LOGIN, (string) $userId, $code);
    }

    public function issueForRegistration(string $token, string $email, string $name): bool
    {
        $subject = __('Verify your :app account', ['app' => SiteBrand::displayName()]);

        return $this->issue(self::PURPOSE_REGISTRATION, $token, $email, $name, $subject);
    }

    public function verifyRegistration(string $token, string $code): bool
    {
        return $this->verify(self::PURPOSE_REGISTRATION, $token, $code);
    }

    public function issueForOtpSettingsEnable(User $user): bool
    {
        $subject = __('Confirm login OTP on :app', ['app' => SiteBrand::displayName()]);

        return $this->issue(self::PURPOSE_OTP_SETTINGS_ENABLE, (string) $user->id, (string) $user->email, (string) $user->name, $subject);
    }

    public function verifyOtpSettingsEnable(int $userId, string $code): bool
    {
        return $this->verify(self::PURPOSE_OTP_SETTINGS_ENABLE, (string) $userId, $code);
    }
}
