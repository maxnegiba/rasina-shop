<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use LogicException;

class ProductionSecurityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! app()->isProduction()) {
            return;
        }

        $violations = [];

        if ((bool) config('app.debug')) {
            $violations[] = 'APP_DEBUG must be false.';
        }

        if (! str_starts_with(strtolower((string) config('app.url')), 'https://')) {
            $violations[] = 'APP_URL must use https://.';
        }

        if (! in_array((string) config('session.driver'), ['database', 'redis'], true)) {
            $violations[] = 'SESSION_DRIVER must be database or redis.';
        }

        if ((bool) config('session.encrypt') !== true) {
            $violations[] = 'SESSION_ENCRYPT must be true.';
        }

        if ((bool) config('session.secure') !== true) {
            $violations[] = 'SESSION_SECURE_COOKIE must be true.';
        }

        if ((bool) config('session.http_only') !== true) {
            $violations[] = 'SESSION_HTTP_ONLY must be true.';
        }

        if (! in_array((string) config('session.same_site'), ['lax', 'strict'], true)) {
            $violations[] = 'SESSION_SAME_SITE must be lax or strict.';
        }

        if (trim((string) config('services.stripe.secret')) === '') {
            $violations[] = 'STRIPE_SECRET must be configured.';
        }

        if (trim((string) config('services.stripe.webhook_secret')) === '') {
            $violations[] = 'STRIPE_WEBHOOK_SECRET must be configured.';
        }

        if ($violations !== []) {
            throw new LogicException("Unsafe production configuration:\n- ".implode("\n- ", $violations));
        }
    }
}
