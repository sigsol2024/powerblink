<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Support\CurrencyCatalog;
use App\Support\SiteCurrencyPreference;
use App\Support\SiteCurrencyUi;
use App\Support\SiteSettingDefaults;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CurrencyPreferenceController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        // Admin-controlled default currency: public currency switching is disabled.
        return response()->json([
            'success' => false,
            'message' => 'Currency switching is disabled.',
        ], 403);

        $site = SiteSettingDefaults::mergeWithDatabase(SiteSetting::allKeyed());
        $displayVersion = SiteCurrencyPreference::displayVersion($site);

        SiteCurrencyPreference::storeVisitorPreference($request, $currency, $displayVersion);
        if ($request->user()) {
            $request->user()->forceFill([
                'preferred_currency' => $currency,
                'currency_selection_prompt_dismissed' => (bool) ($data['markAsShown'] ?? false),
            ])->save();
        } elseif (($data['markAsShown'] ?? false) === true) {
            $request->session()->put('currency_selection_prompt_dismissed', true);
        }

        SiteCurrencyUi::flush();
        $currencyUi = SiteCurrencyUi::resolve();
        $currencyUi['selected'] = $currency;
        $currencyUi['promptDismissed'] = (bool) $request->session()->get('currency_selection_prompt_dismissed', false);

        foreach (SiteCurrencyPreference::preferenceCookies($currency, $displayVersion) as $cookie) {
            Cookie::queue($cookie['name'], $cookie['value'], $cookie['minutes']);
        }

        return response()->json([
            'success' => true,
            'currency' => $currency,
            'currency_ui' => $currencyUi,
        ]);
    }
}

