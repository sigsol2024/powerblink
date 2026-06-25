<?php

namespace App\Services;

use App\Models\InstallmentPlan;
use App\Models\Player;
use App\Models\PlayerDocument;
use App\Models\Registration;
use App\Models\RegistrationPayment;
use App\Services\Mail\OutboundMailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RegistrationPaymentCompletionService
{
    public function __construct(
        private readonly OutboundMailService $mailer,
        private readonly PortalAccountProvisioningService $portalAccounts,
    ) {
    }

    /**
     * @param  array<string, mixed>  $gatewayPayload
     */
    public function complete(RegistrationPayment $payment, array $gatewayPayload): bool
    {
        $txData = is_array($gatewayPayload['data'] ?? null) ? $gatewayPayload['data'] : [];
        $txStatus = (string) ($txData['status'] ?? '');
        $amountPaid = (int) ($txData['amount'] ?? 0);
        $apiSuccess = (bool) ($gatewayPayload['status'] ?? false);

        if (! $apiSuccess || $txStatus !== 'success') {
            return false;
        }

        $result = DB::transaction(function () use ($payment, $gatewayPayload, $amountPaid): array {
            $payment = RegistrationPayment::query()->whereKey($payment->id)->lockForUpdate()->first();
            if (! $payment) {
                return ['ok' => false, 'registration' => null, 'player' => null];
            }

            $registration = Registration::query()
                ->whereKey($payment->registration_id)
                ->lockForUpdate()
                ->first();

            if (! $registration || $registration->status !== 'awaiting_payment') {
                return ['ok' => false, 'registration' => null, 'player' => null];
            }

            if ($amountPaid > 0 && $amountPaid < (int) $payment->amount) {
                return ['ok' => false, 'registration' => null, 'player' => null];
            }

            $payment->update([
                'status' => 'success',
                'paid_at' => $payment->paid_at ?? now(),
                'gateway_payload' => $gatewayPayload,
            ]);

            $registration->update([
                'payment_token_used_at' => now(),
                'status' => 'activated',
            ]);

            $player = $this->activatePlayer($registration);
            $payment->update(['player_id' => $player->id]);

            $this->syncEmergencyContactToGuardian($registration);
            $this->createInstallmentPlansIfNeeded($registration, $player, $payment);

            $registration->loadMissing(['guardian', 'program']);
            if ($registration->guardian) {
                $this->portalAccounts->provisionParentForGuardian($registration->guardian);
            }

            return ['ok' => true, 'registration' => $registration, 'player' => $player];
        });

        if ($result['ok'] && $result['registration'] && $result['player']) {
            $this->sendActivationEmail(
                $result['registration']->fresh(['guardian', 'program']),
                $result['player'],
            );
        }

        return $result['ok'];
    }

    public function fulfillPaidRegistration(Registration $registration): void
    {
        $payload = DB::transaction(function () use ($registration): ?array {
            $registration = Registration::query()->whereKey($registration->id)->lockForUpdate()->first();
            if (! $registration || $registration->status !== 'activated') {
                return null;
            }

            if ($registration->player()->exists()) {
                $registration->loadMissing('guardian');
                if ($registration->guardian) {
                    $this->portalAccounts->provisionParentForGuardian($registration->guardian);
                }

                return null;
            }

            $player = $this->activatePlayer($registration);
            $registration->loadMissing('guardian');
            if ($registration->guardian) {
                $this->portalAccounts->provisionParentForGuardian($registration->guardian);
            }

            return ['registration' => $registration, 'player' => $player];
        });

        if ($payload) {
            $this->sendActivationEmail(
                $payload['registration']->fresh(['guardian', 'program']),
                $payload['player'],
            );
        }
    }

    private function activatePlayer(Registration $registration): Player
    {
        $existing = $registration->player;
        if ($existing) {
            return $existing;
        }

        $registration->loadMissing(['season', 'program', 'guardian']);

        $player = Player::query()->create([
            'registration_id' => $registration->id,
            'guardian_id' => $registration->guardian_id,
            'program_id' => $registration->program_id,
            'season_id' => $registration->season_id,
            'player_code' => $this->generatePlayerCode($registration),
            'photo_media_id' => $registration->profile_photo_media_id,
            'name' => $registration->player_name,
            'date_of_birth' => $registration->date_of_birth,
            'nationality' => $registration->nationality,
            'primary_position' => $registration->primary_position,
            'secondary_position' => $registration->secondary_position,
            'years_experience' => $registration->years_experience,
            'technical_strengths' => $registration->technical_strengths,
            'allergies' => $registration->allergies,
            'medical_history' => $registration->medical_history,
            'status' => 'active',
        ]);

        PlayerDocument::query()
            ->where('registration_id', $registration->id)
            ->whereNull('player_id')
            ->update(['player_id' => $player->id]);

        return $player;
    }

    private function generatePlayerCode(Registration $registration): string
    {
        $year = $registration->season?->start_date?->year ?? now()->year;

        do {
            $sequence = Player::query()
                ->where('season_id', $registration->season_id)
                ->count() + 1;
            $code = sprintf('PB-%d-%04d', $year, $sequence);
        } while (Player::query()->where('player_code', $code)->exists());

        return $code;
    }

    private function syncEmergencyContactToGuardian(Registration $registration): void
    {
        $guardian = $registration->guardian;
        if (! $guardian) {
            return;
        }

        $updates = [];
        if ($guardian->emergency_contact_name === null && $registration->emergency_contact_name) {
            $updates['emergency_contact_name'] = $registration->emergency_contact_name;
        }
        if ($guardian->emergency_contact_phone === null && $registration->emergency_contact_phone) {
            $updates['emergency_contact_phone'] = $registration->emergency_contact_phone;
        }
        if ($guardian->emergency_contact_relationship === null && $registration->emergency_contact_relationship) {
            $updates['emergency_contact_relationship'] = $registration->emergency_contact_relationship;
        }

        if ($updates !== []) {
            $guardian->update($updates);
        }
    }

    private function createInstallmentPlansIfNeeded(Registration $registration, Player $player, RegistrationPayment $payment): void
    {
        if ($registration->payment_plan !== 'installments') {
            return;
        }

        $registration->loadMissing('program');
        $monthlyFee = (int) ($registration->program?->monthly_fee ?? 0);
        if ($monthlyFee <= 0) {
            return;
        }

        for ($i = 1; $i <= 3; $i++) {
            InstallmentPlan::query()->firstOrCreate(
                [
                    'registration_id' => $registration->id,
                    'due_date' => now()->addMonths($i)->startOfMonth()->toDateString(),
                ],
                [
                    'player_id' => $player->id,
                    'amount' => $monthlyFee,
                    'status' => 'pending',
                    'registration_payment_id' => $payment->id,
                ],
            );
        }
    }

    private function sendActivationEmail(Registration $registration, Player $player): void
    {
        $guardian = $registration->guardian;
        if (! $guardian?->email) {
            return;
        }

        $html = view('emails.registrations.activated', [
            'registration' => $registration,
            'player' => $player,
        ])->render();

        try {
            $this->mailer->send(
                $guardian->email,
                $guardian->name,
                __('Welcome to PowerBlink FC — :code', ['code' => $player->player_code]),
                $html,
            );
        } catch (Throwable $e) {
            Log::warning('Registration activation email failed', [
                'registration_id' => $registration->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
