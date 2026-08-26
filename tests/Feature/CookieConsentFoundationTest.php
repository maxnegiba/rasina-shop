<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CookieConsentFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_include_the_romanian_cookie_consent_foundation(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Acest site folosește cookie-uri')
            ->assertSee('Acceptă toate')
            ->assertSee('Doar necesare')
            ->assertSee('Preferințe cookie')
            ->assertSee('Necesare')
            ->assertSee('Analiză')
            ->assertSee('Marketing')
            ->assertDontSee('js/privacy-preferences.js', escape: false);

        $appEntry = file_get_contents(resource_path('js/app.js'));

        $this->assertIsString($appEntry);
        $this->assertStringContainsString("import './privacy-preferences';", $appEntry);
    }

    public function test_tracking_stays_disabled_in_the_example_environment(): void
    {
        $example = file_get_contents(base_path('.env.example'));

        $this->assertStringContainsString('MARKETING_TRACKING_ENABLED=false', $example);
        $this->assertStringContainsString("GTM_CONTAINER_ID=\n", $example);
        $this->assertStringContainsString("GA4_MEASUREMENT_ID=\n", $example);
        $this->assertStringContainsString("META_PIXEL_ID=\n", $example);
        $this->assertStringContainsString("META_CAPI_ACCESS_TOKEN=\n", $example);
        $this->assertStringContainsString("TIKTOK_PIXEL_ID=\n", $example);
    }
}
