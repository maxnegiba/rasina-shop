<?php

namespace App\Support;

class ProductionSecurity
{
    /** @return list<string> */
    public function violations(): array
    {
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

        if ((string) config('mail.default') === 'log') {
            $violations[] = 'MAIL_MAILER must deliver email in production; the log mailer would break admin MFA.';
        }

        if (trim((string) config('services.stripe.key')) === '') {
            $violations[] = 'STRIPE_KEY must be configured.';
        }

        if (trim((string) config('services.stripe.secret')) === '') {
            $violations[] = 'STRIPE_SECRET must be configured.';
        }

        if (trim((string) config('services.stripe.webhook_secret')) === '') {
            $violations[] = 'STRIPE_WEBHOOK_SECRET must be configured.';
        }

        return $violations;
    }
}
