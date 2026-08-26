<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleTagManagerFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_gtm_is_not_injected_while_tracking_is_disabled(): void
    {
        config()->set('marketing.tracking_enabled', false);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertDontSee('window.mtdLoadGtm', escape: false)
            ->assertDontSee('googletagmanager.com/gtm.js', escape: false)
            ->assertDontSee('googletagmanager.com/ns.html', escape: false);
    }

    public function test_tracking_enabled_renders_a_consent_aware_deferred_gtm_loader(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('window.mtdLoadGtm', escape: false)
            ->assertSee('var containerId = "GTM-TEST123"', escape: false)
            ->assertSee('hasOptionalConsent(readConsent())', escape: false)
            ->assertSee('window.setTimeout(function()', escape: false)
            ->assertSee('}, 5000);', escape: false)
            ->assertSee('https://www.googletagmanager.com/gtm.js?id=', escape: false)
            ->assertDontSee('https://www.googletagmanager.com/ns.html', escape: false);

        $csp = (string) $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('https://www.googletagmanager.com', $csp);
        $this->assertStringContainsString('https://tagmanager.google.com', $csp);
        $this->assertStringContainsString('https://*.google-analytics.com', $csp);
        $this->assertStringContainsString('https://*.analytics.google.com', $csp);
        $this->assertStringContainsString('https://connect.facebook.net', $csp);
        $this->assertStringContainsString('https://www.facebook.com', $csp);
        $this->assertStringContainsString('https://analytics.tiktok.com', $csp);
        $this->assertStringContainsString('https://analytics-ipv6.tiktokw.us', $csp);
    }

    public function test_google_marketing_csp_sources_are_absent_when_tracking_is_disabled(): void
    {
        config()->set('marketing.tracking_enabled', false);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        $response = $this->get('/');
        $csp = (string) $response->headers->get('Content-Security-Policy');

        $this->assertStringNotContainsString('https://tagmanager.google.com', $csp);
        $this->assertStringNotContainsString('https://*.google-analytics.com', $csp);
        $this->assertStringNotContainsString('https://*.analytics.google.com', $csp);
        $this->assertStringNotContainsString('https://connect.facebook.net', $csp);
        $this->assertStringNotContainsString('https://www.facebook.com', $csp);
        $this->assertStringNotContainsString('https://analytics.tiktok.com', $csp);
        $this->assertStringNotContainsString('https://analytics-ipv6.tiktokw.us', $csp);
    }

    public function test_invalid_gtm_container_id_is_never_injected(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'not-a-gtm-id');

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertDontSee('window.mtdLoadGtm', escape: false)
            ->assertDontSee('googletagmanager.com/gtm.js', escape: false)
            ->assertDontSee('googletagmanager.com/ns.html', escape: false);
    }
}
