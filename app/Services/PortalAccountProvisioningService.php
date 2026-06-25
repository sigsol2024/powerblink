<?php

namespace App\Services;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Throwable;

class PortalAccountProvisioningService
{
    /**
     * Ensure the guardian has a linked parent portal user account.
     *
     * @return array{user: User|null, created: bool, password_reset_sent: bool}
     */
    public function provisionParentForGuardian(Guardian $guardian): array
    {
        $email = strtolower(trim((string) $guardian->email));
        if ($email === '') {
            return ['user' => null, 'created' => false, 'password_reset_sent' => false];
        }

        $created = false;
        $passwordResetSent = false;

        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => $guardian->name ?: __('Parent'),
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);
            $created = true;
        }

        if (! $user->hasRole('parent')) {
            $user->assignRole('parent');
        }

        if ($guardian->user_id !== $user->id) {
            $guardian->update(['user_id' => $user->id]);
        }

        if ($created) {
            $passwordResetSent = $this->sendPasswordSetupInvite($user);
        }

        return [
            'user' => $user,
            'created' => $created,
            'password_reset_sent' => $passwordResetSent,
        ];
    }

    public function sendPasswordSetupInvite(User $user): bool
    {
        try {
            $token = Password::broker()->createToken($user);
            $user->notify(new ResetPassword($token));

            return true;
        } catch (Throwable $e) {
            Log::warning('Parent portal password invite failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
