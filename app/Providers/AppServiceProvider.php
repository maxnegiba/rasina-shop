<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // On production, absolute route(), asset() and Open Graph URLs use
        // the same origin as the public canonical and sitemap.
        // Staging/local stay on their own host and must remain non-indexable.
        if (app()->environment('production')) {
            URL::forceRootUrl((string) config('seo.canonical_origin'));
            URL::forceScheme('https');
        }
    }
}
