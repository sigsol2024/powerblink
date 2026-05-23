<?php

namespace Tests\Unit;

use App\Models\SiteSetting;
use App\Support\SiteCurrencyPreference;
use App\Support\SiteSettingDefaults;
use App\View\Composers\SiteCurrencyComposer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class SiteCurrencyComposerTest extends TestCase
{
    use RefreshDatabase;

    public function test_stale_session_preference_falls_back_to_site_default(): void
    {
        SiteSetting::setValue('currency_code', 'NGN');
        SiteSetting::setValue(SiteCurrencyPreference::VERSION_KEY, '2');

        $this->startSession();
        session([
            SiteCurrencyPreference::SESSION_CURRENCY => 'USD',
            SiteCurrencyPreference::SESSION_VERSION => 1,
        ]);

        $view = View::make('layouts.site');
        $view->with('site', SiteSettingDefaults::mergeWithDatabase(SiteSetting::allKeyed()));

        app(SiteCurrencyComposer::class)->compose($view);

        $currencyUi = $view->getData()['currencyUi'];
        $this->assertSame('NGN', $currencyUi['default']);
        $this->assertSame('NGN', $currencyUi['selected']);
        $this->assertFalse($currencyUi['preferenceCurrent']);
    }
}
