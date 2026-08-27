<?php

namespace Tests\Feature;

use App\Http\Middleware\InjectUmamiAnalytics;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UmamiAnalyticsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'marketing.umami.enabled' => true,
            'marketing.umami.script_url' => 'https://analytics.mtdart.ro/script.js',
            'marketing.umami.website_id' => '0e991672-a898-4eb4-b4e6-c3c77731f470',
            'marketing.umami.domains' => 'mtdart.ro,www.mtdart.ro',
        ]);
    }

    public function test_it_injects_a_consent_aware_umami_loader_into_storefront_html(): void
    {
        $request = Request::create('/', 'GET');
        $middleware = app(InjectUmamiAnalytics::class);

        $response = $middleware->handle(
            $request,
            fn (): Response => new Response('<!doctype html><html><head><title>MTD</title></head><body></body></html>', 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]),
        );

        $content = (string) $response->getContent();

        $this->assertStringContainsString('Consent-aware Umami analytics', $content);
        $this->assertStringContainsString('https://analytics.mtdart.ro/script.js', $content);
        $this->assertStringContainsString('0e991672-a898-4eb4-b4e6-c3c77731f470', $content);
        $this->assertStringContainsString('if (loaded || !analyticsGranted())', $content);
        $this->assertStringContainsString('.js-lcc-accept, .js-lcc-settings-save', $content);
    }

    public function test_it_does_not_inject_when_umami_is_disabled(): void
    {
        config(['marketing.umami.enabled' => false]);

        $request = Request::create('/', 'GET');
        $middleware = app(InjectUmamiAnalytics::class);

        $response = $middleware->handle(
            $request,
            fn (): Response => new Response('<html><head></head><body></body></html>', 200, [
                'Content-Type' => 'text/html',
            ]),
        );

        $this->assertStringNotContainsString('analytics.mtdart.ro/script.js', (string) $response->getContent());
    }

    public function test_it_fails_closed_for_an_invalid_website_id(): void
    {
        config(['marketing.umami.website_id' => 'not-a-valid-uuid']);

        $request = Request::create('/', 'GET');
        $middleware = app(InjectUmamiAnalytics::class);

        $response = $middleware->handle(
            $request,
            fn (): Response => new Response('<html><head></head><body></body></html>', 200, [
                'Content-Type' => 'text/html',
            ]),
        );

        $this->assertStringNotContainsString('analytics.mtdart.ro/script.js', (string) $response->getContent());
    }

    public function test_csp_allows_only_the_umami_origin_when_umami_is_enabled(): void
    {
        config([
            'marketing.tracking_enabled' => false,
            'marketing.umami.enabled' => true,
        ]);

        // SecurityHeaders intentionally skips CSP in the local environment.
        // Force a non-local environment for this isolated middleware test so
        // the assertion tests the production CSP branch deterministically,
        // regardless of the runner's inherited APP_ENV value.
        $originalEnvironment = app()->environment();
        app()->detectEnvironment(fn (): string => 'testing');

        try {
            $request = Request::create('/', 'GET');
            $middleware = app(SecurityHeaders::class);

            $response = $middleware->handle(
                $request,
                fn (): Response => new Response('ok', 200),
            );

            $csp = (string) $response->headers->get('Content-Security-Policy');

            $this->assertStringContainsString('script-src', $csp);
            $this->assertStringContainsString('connect-src', $csp);
            $this->assertSame(2, substr_count($csp, 'https://analytics.mtdart.ro'));
        } finally {
            app()->detectEnvironment(fn (): string => $originalEnvironment);
        }
    }
}
