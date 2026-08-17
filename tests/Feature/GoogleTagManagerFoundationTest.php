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
            ->assertDontSee('googletagmanager.com/gtm.js', escape: false)
            ->assertDontSee('googletagmanager.com/ns.html', escape: false);
    }

    public function test_gtm_is_injected_when_tracking_is_enabled_and_container_id_is_valid(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee("'GTM-TEST123'", escape: false)
            ->assertSee('https://www.googletagmanager.com/gtm.js?id=', escape: false)
            ->assertSee('https://www.googletagmanager.com/ns.html?id=GTM-TEST123', escape: false);

        $csp = (string) $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('https://www.googletagmanager.com', $csp);
    }

    public function test_invalid_gtm_container_id_is_never_injected(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'not-a-gtm-id');

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertDontSee('googletagmanager.com/gtm.js', escape: false)
            ->assertDontSee('googletagmanager.com/ns.html', escape: false);
    }
}
