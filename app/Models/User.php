<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Services\Mail\OutboundMailService;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'email_login_otp_enabled',
        'currency_selection_prompt_dismissed',
        'email_verified_at',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_login_otp_enabled' => 'boolean',
            'currency_selection_prompt_dismissed' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    public function coachProfile(): HasOne
    {
        return $this->hasOne(Coach::class);
    }

    public function guardianProfile(): HasOne
    {
        return $this->hasOne(Guardian::class);
    }

    public function playerProfile(): HasOne
    {
        return $this->hasOne(Player::class);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isCoach(): bool
    {
        return $this->hasRole('coach');
    }

    public function isParent(): bool
    {
        return $this->hasRole('parent');
    }

    public function isPlayer(): bool
    {
        return $this->hasRole('player');
    }

    /** @deprecated Use isCoach() — legacy ecommerce editor role replaced by coach */
    public function isEditor(): bool
    {
        return $this->isCoach();
    }

    public function isStaff(): bool
    {
        return $this->canAccessAdminPanel();
    }

    public function isMember(): bool
    {
        return $this->isCoach() || $this->isParent() || $this->isPlayer();
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->isAdmin();
    }

    public function staffHomeRoute(): string
    {
        if ($this->canAccessAdminPanel()) {
            return route('admin.dashboard', absolute: false);
        }

        if ($this->isMember()) {
            return route('portal.dashboard', absolute: false);
        }

        return route('dashboard', absolute: false);
    }

    public function loginRedirectPath(): string
    {
        if ($this->canAccessAdminPanel()) {
            session()->forget('url.intended');

            return $this->staffHomeRoute();
        }

        if ($this->isMember()) {
            session()->forget('url.intended');

            return route('portal.dashboard', absolute: false);
        }

        $intended = session()->pull('url.intended');

        return (is_string($intended) && $intended !== '')
            ? $intended
            : route('dashboard', absolute: false);
    }

    public function sendPasswordResetNotification($token): void
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        $html = view('emails.password-reset', [
            'user' => $this,
            'resetUrl' => $url,
        ])->render();

        app(OutboundMailService::class)->send(
            $this->email,
            (string) $this->name,
            __('Reset your password'),
            $html,
            $this->email,
            (string) $this->name
        );
    }
}
