<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\Mail\OutboundMailService;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
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
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isEditor(): bool
    {
        return $this->hasRole('editor');
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isEditor();
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->isStaff();
    }

    public function staffHomeRoute(): string
    {
        if ($this->isAdmin()) {
            return route('admin.dashboard', absolute: false);
        }

        if ($this->isEditor()) {
            return route('dashboard.vehicles.index', absolute: false);
        }

        return route('dashboard', absolute: false);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Saved / wishlist vehicles (approved listings the user bookmarked).
     */
    public function favoriteVehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_favorites')->withTimestamps();
    }

    public function vendorProfile(): HasOne
    {
        return $this->hasOne(VendorProfile::class);
    }

    /**
     * Send the password reset notification using the outbound mailer and branded template.
     */
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
