<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Support\SiteSettingDefaults;
use App\View\Composers\CmsNavComposer;
use App\View\Composers\FaqNavComposer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
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

        View::composer('layouts.site', CmsNavComposer::class);
        View::composer('layouts.site', FaqNavComposer::class);
    }
}
