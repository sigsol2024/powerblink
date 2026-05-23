<?php

namespace Tests\Unit;

use App\Models\SiteSetting;
use App\Support\CurrencyDisplay;
use App\Support\SiteCurrencyPreference;
use App\Support\SiteCurrencyUi;
use App\Support\SiteSettingDefaults;
use App\View\Composers\SiteCurrencyComposer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class SiteCurrencyComposerTest extends TestCase
{
    use RefreshDatabase;

    public function test_format_for_site_uses_ngn_when_default_is_ngn(): void
    {
        SiteSetting::setValue('currency_code', 'NGN');
        SiteSetting::setValue(SiteCurrencyPreference::VERSION_KEY, '2');

        View::share('site', SiteSettingDefaults::mergeWithDatabase(SiteSetting::allKeyed()));
        SiteCurrencyUi::flush();

        $formatted = CurrencyDisplay::formatForSite(35_000_000, 0);

        $this->assertStringStartsWith('₦', $formatted);
        $this->assertStringNotContainsString('$', $formatted);
    }

    public function test_stale_session_preference_falls_back_to_site_default(): void
    {
        SiteSetting::setValue('currency_code', 'NGN');
        SiteSetting::setValue(SiteCurrencyPreference::VERSION_KEY, '2');

        $this->startSession();
        session([
            SiteCurrencyPreference::SESSION_CURRENCY => 'USD',
            SiteCurrencyPreference::SESSION_VERSION => 1,
        ]);

        View::share('site', SiteSettingDefaults::mergeWithDatabase(SiteSetting::allKeyed()));
        SiteCurrencyUi::flush();

        $view = View::make('layouts.site');
        app(SiteCurrencyComposer::class)->compose($view);

        $currencyUi = $view->getData()['currencyUi'];
        $this->assertSame('NGN', $currencyUi['default']);
        $this->assertSame('NGN', $currencyUi['selected']);
        $this->assertTrue($currencyUi['preferenceCurrent']);
    }
}
