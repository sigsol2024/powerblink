<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Support\SiteSettingDefaults;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Shared hosting MySQL (utf8mb4): varchar(255) unique/primary keys exceed index limits.
        Schema::defaultStringLength(191);

        // When APP_URL is HTTPS (or we're already on HTTPS), force every generated URL — including
        // form actions, asset(), and route() — to use https. cPanel terminates SSL at a proxy and
        // PHP can still see scheme=http internally; without this, `route('cart.add')` etc. emit
        // http:// URLs, the form posts to http, the browser drops the secure session cookie, and
        // every POST returns 419 "CSRF token mismatch".
        if (Str::startsWith((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Password::defaults(function () {
            return Password::min(10)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        $fromDb = [];
        try {
            if (Schema::hasTable('site_settings')) {
                $fromDb = SiteSetting::allKeyed();
            }
        } catch (\Throwable) {
            $fromDb = [];
        }

        $site = SiteSettingDefaults::mergeWithDatabase($fromDb);
        View::share('site', $site);
    }
}
