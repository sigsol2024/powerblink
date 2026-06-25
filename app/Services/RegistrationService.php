<?php

namespace App\Services;

use App\Models\Guardian;
use App\Models\Program;
use App\Models\Registration;
use App\Models\Season;
use App\Models\User;
use App\Services\Mail\OutboundMailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class RegistrationService
{
    public function __construct(
        private readonly OutboundMailService $mailer,
        private readonly AcademyNotificationService $notifications,
    ) {
    }

    public function resolveActiveSeason(): ?Season
    {
        return Season::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first()
            ?? Season::query()->orderByDesc('start_date')->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function submit(array $data): Registration
    {
        $season = $this->resolveActiveSeason();
        if (! $season) {
            throw new \RuntimeException('No active season is configured for registrations.');
        }

        $program = Program::query()
            ->whereKey((int) ($data['program_id'] ?? 0))
            ->where('season_id', $season->id)
            ->where('is_active', true)
            ->firstOrFail();

        $registration = DB::transaction(function () use ($data, $season, $program): Registration {
            $guardian = Guardian::query()->create([
                'name' => (string) $data['guardian_name'],
                'relationship' => (string) ($data['guardian_relationship'] ?? ''),
                'phone' => (string) ($data['guardian_phone'] ?? ''),
                'email' => (string) $data['guardian_email'],
                'address' => (string) ($data['guardian_address'] ?? ''),
            ]);

            return Registration::query()->create([
                'reference_code' => $this->generateReferenceCode(),
                'season_id' => $season->id,
                'program_id' => $program->id,
                'guardian_id' => $guardian->id,
                'status' => 'pending_review',
                'payment_plan' => (string) ($data['payment_plan'] ?? 'lump_sum'),
                'player_name' => (string) $data['player_name'],
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'nationality' => (string) ($data['nationality'] ?? ''),
                'primary_position' => (string) ($data['primary_position'] ?? ''),
                'secondary_position' => (string) ($data['secondary_position'] ?? ''),
                'years_experience' => isset($data['years_experience']) ? (int) $data['years_experience'] : null,
                'technical_strengths' => (string) ($data['technical_strengths'] ?? ''),
                'allergies' => (string) ($data['allergies'] ?? ''),
                'medical_history' => (string) ($data['medical_history'] ?? ''),
                'fitness_certified' => (bool) ($data['fitness_certified'] ?? false),
                'emergency_contact_name' => (string) ($data['emergency_contact_name'] ?? ''),
                'emergency_contact_phone' => (string) ($data['emergency_contact_phone'] ?? ''),
                'emergency_contact_relationship' => (string) ($data['emergency_contact_relationship'] ?? ''),
                'submitted_at' => now(),
            ]);
        });

        $this->notifications->notifyPermissionHolders(
            'registrations.view',
            __('New registration'),
            __(':player applied for :program (:code).', [
                'player' => $registration->player_name,
                'program' => $program->name,
                'code' => $registration->reference_code,
            ]),
            route('admin.registrations.index', ['status' => 'pending_review'], false),
        );

        return $registration;
    }

    public function approve(Registration $registration, User $admin): Registration
    {
        if ($registration->status !== 'pending_review') {
            throw new \InvalidArgumentException('Only pending registrations can be approved.');
        }

        $registration->update([
            'status' => 'awaiting_payment',
            'payment_token' => (string) Str::uuid(),
            'payment_token_expires_at' => now()->addDays(7),
            'payment_token_used_at' => null,
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'rejected_reason' => null,
            'rejected_at' => null,
        ]);

        $registration = $registration->fresh(['guardian', 'program']);
        $this->sendApprovalEmail($registration);
        $this->notifyGuardianUser(
            $registration,
            __('Registration approved'),
            __('Your application :code is approved. Check your email for the payment link.', ['code' => $registration->reference_code]),
        );

        return $registration;
    }

    public function reject(Registration $registration, string $reason, User $admin): Registration
    {
        if ($registration->status !== 'pending_review') {
            throw new \InvalidArgumentException('Only pending registrations can be rejected.');
        }

        $registration->update([
            'status' => 'rejected',
            'rejected_reason' => trim($reason),
            'rejected_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
            'payment_token' => null,
            'payment_token_expires_at' => null,
        ]);

        $registration = $registration->fresh(['guardian', 'program']);
        $this->sendRejectionEmail($registration);
        $this->notifyGuardianUser(
            $registration,
            __('Registration update'),
            __('Application :code was not approved at this time.', ['code' => $registration->reference_code]),
        );

        return $registration;
    }

    public function regeneratePaymentToken(Registration $registration): Registration
    {
        if ($registration->status !== 'awaiting_payment') {
            throw new \InvalidArgumentException('Payment token can only be regenerated for awaiting_payment registrations.');
        }

        $registration->update([
            'payment_token' => (string) Str::uuid(),
            'payment_token_expires_at' => now()->addDays(7),
            'payment_token_used_at' => null,
        ]);

        $this->sendApprovalEmail($registration->fresh(['guardian', 'program']));

        return $registration;
    }

    public function computeRegistrationFee(Registration $registration): int
    {
        $registration->loadMissing('program');

        return max(100, (int) ($registration->program?->registration_fee ?? 0));
    }

    public function generateReferenceCode(): string
    {
        $year = now()->year;

        do {
            $sequence = Registration::query()
                ->whereYear('created_at', $year)
                ->count() + 1;
            $code = sprintf('REG-%d-%04d', $year, $sequence);
        } while (Registration::query()->where('reference_code', $code)->exists());

        return $code;
    }

    private function notifyGuardianUser(Registration $registration, string $title, string $body): void
    {
        $guardian = $registration->guardian;
        if (! $guardian?->user_id) {
            return;
        }

        $user = User::query()->find($guardian->user_id);
        $this->notifications->notifyUser($user, $title, $body, route('portal.dashboard', absolute: false));
    }

    private function sendApprovalEmail(Registration $registration): void
    {
        $guardian = $registration->guardian;
        if (! $guardian?->email) {
            return;
        }

        $payUrl = route('registration.pay.show', ['token' => $registration->payment_token], true);
        $feeKobo = $this->computeRegistrationFee($registration);
        $html = view('emails.registrations.approved', [
            'registration' => $registration,
            'payUrl' => $payUrl,
            'feeDisplay' => format_currency($feeKobo / 100),
        ])->render();

        try {
            $this->mailer->send(
                $guardian->email,
                $guardian->name,
                __('Registration approved — :code', ['code' => $registration->reference_code]),
                $html,
            );
        } catch (Throwable) {
            // Email failure must not roll back approval.
        }
    }

    private function sendRejectionEmail(Registration $registration): void
    {
        $guardian = $registration->guardian;
        if (! $guardian?->email) {
            return;
        }

        $html = view('emails.registrations.rejected', [
            'registration' => $registration,
        ])->render();

        try {
            $this->mailer->send(
                $guardian->email,
                $guardian->name,
                __('Registration update — :code', ['code' => $registration->reference_code]),
                $html,
            );
        } catch (Throwable) {
            //
        }
    }
}
