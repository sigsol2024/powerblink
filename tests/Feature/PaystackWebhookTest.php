<?php

namespace Tests\Feature;

use App\Jobs\ProcessPaystackWebhookPayment;
use App\Models\Guardian;
use App\Models\Program;
use App\Models\Registration;
use App\Models\RegistrationPayment;
use App\Models\Season;
use Database\Seeders\AcademyPermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaystackWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AcademyPermissionsSeeder::class);
    }

    public function test_webhook_dispatches_retry_job_when_verify_fails(): void
    {
        Queue::fake();

        $season = Season::query()->create([
            'name' => '2026 Season',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $program = Program::query()->create([
            'season_id' => $season->id,
            'name' => 'U13 Development',
            'age_group' => 'U13',
            'registration_fee' => 3500000,
            'monthly_fee' => 6500000,
            'is_active' => true,
        ]);

        $guardian = Guardian::query()->create([
            'name' => 'Webhook Parent',
            'email' => 'webhook-parent@example.com',
            'phone' => '+2348000000099',
        ]);

        $registration = Registration::query()->create([
            'reference_code' => 'REG-WH-0001',
            'season_id' => $season->id,
            'program_id' => $program->id,
            'guardian_id' => $guardian->id,
            'status' => 'awaiting_payment',
            'payment_plan' => 'lump_sum',
            'payment_token' => (string) Str::uuid(),
            'player_name' => 'Webhook Player',
            'date_of_birth' => '2013-01-01',
            'submitted_at' => now(),
        ]);

        $payment = RegistrationPayment::query()->create([
            'registration_id' => $registration->id,
            'season_id' => $season->id,
            'type' => 'registration_fee',
            'provider' => 'paystack',
            'reference' => 'REG_WEBHOOK_FAIL',
            'status' => 'pending',
            'amount' => 3500000,
            'currency' => 'NGN',
        ]);

        Http::fake([
            'https://api.paystack.co/transaction/verify/*' => Http::response([], 500),
        ]);

        $payload = [
            'event' => 'charge.success',
            'data' => ['reference' => $payment->reference],
        ];

        $body = json_encode($payload);

        $response = $this->call(
            'POST',
            route('payment.paystack.webhook'),
            server: [
                'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac('sha512', $body, (string) config('services.paystack.webhook_secret')),
                'CONTENT_TYPE' => 'application/json',
            ],
            content: $body,
        );

        $response->assertOk();
        Queue::assertPushed(ProcessPaystackWebhookPayment::class);
    }
}
