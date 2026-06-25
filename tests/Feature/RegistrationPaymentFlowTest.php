<?php

namespace Tests\Feature;

use App\Models\Guardian;
use App\Models\Program;
use App\Models\Registration;
use App\Models\RegistrationPayment;
use App\Models\Season;
use App\Models\User;
use Database\Seeders\AcademyPermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegistrationPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AcademyPermissionsSeeder::class);
    }

    private function seedRegistration(string $status = 'awaiting_payment'): Registration
    {
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
            'name' => 'Test Parent',
            'email' => 'parent-flow@example.com',
            'phone' => '+2348000000001',
        ]);

        return Registration::query()->create([
            'reference_code' => 'REG-TEST-0001',
            'season_id' => $season->id,
            'program_id' => $program->id,
            'guardian_id' => $guardian->id,
            'status' => $status,
            'payment_plan' => 'lump_sum',
            'payment_token' => (string) Str::uuid(),
            'payment_token_expires_at' => now()->addDays(7),
            'player_name' => 'Flow Test Player',
            'date_of_birth' => '2013-01-01',
            'submitted_at' => now(),
        ]);
    }

    public function test_payment_initialize_is_idempotent_for_pending_payment(): void
    {
        $registration = $this->seedRegistration();
        $existing = RegistrationPayment::query()->create([
            'registration_id' => $registration->id,
            'season_id' => $registration->season_id,
            'type' => 'registration_fee',
            'provider' => 'paystack',
            'reference' => 'REG_EXISTING123',
            'status' => 'pending',
            'amount' => 3500000,
            'currency' => 'NGN',
            'gateway_payload' => [
                'data' => ['authorization_url' => 'https://checkout.paystack.com/resume'],
            ],
        ]);

        Http::fake();

        $response = $this->post(route('registration.pay.initialize', ['token' => $registration->payment_token]));

        $response->assertRedirect('https://checkout.paystack.com/resume');
        $this->assertSame(1, RegistrationPayment::query()->where('registration_id', $registration->id)->count());
        $this->assertTrue($existing->is(RegistrationPayment::query()->first()));
    }

    public function test_payment_completion_links_parent_user(): void
    {
        $registration = $this->seedRegistration();
        $payment = RegistrationPayment::query()->create([
            'registration_id' => $registration->id,
            'season_id' => $registration->season_id,
            'type' => 'registration_fee',
            'provider' => 'paystack',
            'reference' => 'REG_COMPLETE01',
            'status' => 'pending',
            'amount' => 3500000,
            'currency' => 'NGN',
        ]);

        Http::fake([
            'https://api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'amount' => 3500000,
                    'reference' => $payment->reference,
                ],
            ], 200),
        ]);

        $service = app(\App\Services\RegistrationPaymentCompletionService::class);
        $verified = app(\App\Services\PaystackService::class)->verify($payment->reference);

        $this->assertTrue($service->complete($payment, $verified));

        $registration->refresh();
        $guardian = $registration->guardian?->fresh();

        $this->assertSame('activated', $registration->status);
        $this->assertNotNull($registration->player);
        $this->assertNotNull($guardian?->user_id);

        $parent = User::query()->find($guardian->user_id);
        $this->assertNotNull($parent);
        $this->assertTrue($parent->hasRole('parent'));
    }

    public function test_admin_can_approve_pending_registration(): void
    {
        $registration = $this->seedRegistration('pending_review');
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.registrations.approve', $registration))
            ->assertRedirect();

        $registration->refresh();
        $this->assertSame('awaiting_payment', $registration->status);
        $this->assertNotNull($registration->payment_token);
    }
}
