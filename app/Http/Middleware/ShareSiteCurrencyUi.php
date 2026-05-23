<?php

namespace App\Http\Middleware;

use App\Support\SiteCurrencyUi;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Share currency UI on every web request (after session) so child views render correct symbols.
 */
class ShareSiteCurrencyUi
{
    public function handle(Request $request, Closure $next): Response
    {
        SiteCurrencyUi::flush();
        View::share('currencyUi', SiteCurrencyUi::resolve());

        return $next($request);
    }
}
