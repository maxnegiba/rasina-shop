<?php

namespace Tests\Unit;

use App\Support\ProductionSecurity;
use Tests\TestCase;

class ProductionSecurityTest extends TestCase
{
    public function test_hardened_production_configuration_has_no_violations(): void
    {
        config([
            'app.key' => 'base64:'.base64_encode(random_bytes(32)),
            'app.debug' => false,
            'app.url' => 'https://mtdart.ro',
            'session.driver' => 'database',
            'session.encrypt' => true,
            'session.secure' => true,
            'session.http_only' => true,
            'session.same_site' => 'lax',
            'mail.default' => 'smtp',
            'services.stripe.key' => 'pk_example',
            'services.stripe.secret' => 'sk_example',
            'services.stripe.webhook_secret' => 'whsec_example',
        ]);

        $this->assertSame([], app(ProductionSecurity::class)->violations());
    }

    public function test_insecure_production_configuration_is_rejected(): void
    {
        config([
            'app.key' => '',
            'app.debug' => true,
            'app.url' => 'http://mtdart.ro',
            'session.driver' => 'cookie',
            'session.encrypt' => false,
            'session.secure' => false,
            'session.http_only' => false,
            'session.same_site' => 'none',
            'mail.default' => 'log',
            'services.stripe.key' => '',
            'services.stripe.secret' => '',
            'services.stripe.webhook_secret' => '',
        ]);

        $violations = app(ProductionSecurity::class)->violations();

        $this->assertContains('APP_KEY must be configured.', $violations);
        $this->assertContains('APP_DEBUG must be false.', $violations);
        $this->assertContains('APP_URL must use https://.', $violations);
        $this->assertContains('SESSION_DRIVER must be database or redis.', $violations);
        $this->assertContains('SESSION_ENCRYPT must be true.', $violations);
        $this->assertContains('SESSION_SECURE_COOKIE must be true.', $violations);
        $this->assertContains('SESSION_HTTP_ONLY must be true.', $violations);
        $this->assertContains('SESSION_SAME_SITE must be lax or strict.', $violations);
        $this->assertContains('MAIL_MAILER must deliver email in production; the log mailer would break admin MFA.', $violations);
        $this->assertContains('STRIPE_KEY must be configured.', $violations);
        $this->assertContains('STRIPE_SECRET must be configured.', $violations);
        $this->assertContains('STRIPE_WEBHOOK_SECRET must be configured.', $violations);
    }
}
