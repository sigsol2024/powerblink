<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use App\Support\CurrencyDisplay;
use App\Support\SiteCurrencyPreference;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteCurrencyDefaultTest extends TestCase
{
    use RefreshDatabase;

    public function test_currency_display_uses_naira_symbol_when_site_default_is_ngn(): void
    {
        SiteSetting::setValue('currency_code', 'NGN');

        $formatted = CurrencyDisplay::formatAmount(1_500_000, [
            'default' => 'NGN',
            'selected' => 'NGN',
            'symbols' => ['NGN' => '₦'],
            'rates' => ['NGN' => 1.0],
        ], 0);

        $this->assertStringStartsWith('₦', $formatted);
        $this->assertStringNotContainsString('$', $formatted);
    }

    public function test_admin_changing_default_currency_bumps_display_version(): void
    {
        $this->seed(RolesSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        SiteSetting::setValue('currency_code', 'USD');
        SiteSetting::setValue(SiteCurrencyPreference::VERSION_KEY, '1');

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'currency_code' => 'NGN',
                'dealer_hours_label' => 'Hours',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $this->assertSame('NGN', SiteSetting::getValue('currency_code'));
        $this->assertSame('2', SiteSetting::getValue(SiteCurrencyPreference::VERSION_KEY));
        $this->assertSame('NGN', $admin->fresh()->preferred_currency);
    }
}
