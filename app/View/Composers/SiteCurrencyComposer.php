<?php

namespace App\View\Composers;

use App\Support\SiteCurrencyUi;
use Illuminate\View\View;

/**
 * Ensures layouts.site has currencyUi (same payload as ShareSiteCurrencyUi middleware).
 */
class SiteCurrencyComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $site = is_array($data['site'] ?? null) ? $data['site'] : null;
        $view->with('currencyUi', SiteCurrencyUi::resolve($site));
    }
}
