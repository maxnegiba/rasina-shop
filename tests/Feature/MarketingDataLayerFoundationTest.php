<?php

namespace Tests\Feature;

use App\Services\MarketingDataLayer;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MarketingDataLayerFoundationTest extends TestCase
{
    public function test_server_event_is_pushed_before_gtm_without_rendering_a_second_gtm_snippet(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        Route::middleware('web')->get('/_marketing-data-layer', function (MarketingDataLayer $dataLayer) {
            $dataLayer->push('view_product', [
                'ecommerce' => [
                    'currency' => 'RON',
                    'value' => 249.0,
                    'items' => [[
                        'item_id' => '123',
                        'item_name' => 'Cruce test',
                        'price' => 249.0,
                        'quantity' => 1,
                    ]],
                ],
            ]);

            return response('<html><head></head><body>ok</body></html>', 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        });

        $content = $this->get('/_marketing-data-layer')
            ->assertOk()
            ->getContent();

        $this->assertIsString($content);
        $this->assertStringContainsString('window.dataLayer.push({"event":"view_product"', $content);
        $this->assertStringContainsString('"currency":"RON"', $content);
        $this->assertSame(1, substr_count($content, 'googletagmanager.com/gtm.js?id='));
        $this->assertLessThan(
            strpos($content, 'googletagmanager.com/gtm.js?id='),
            strpos($content, 'window.dataLayer.push({"event":"view_product"'),
        );
    }

    public function test_data_layer_payload_is_safely_encoded_for_inline_script_context(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        Route::middleware('web')->get('/_marketing-data-layer-xss', function (MarketingDataLayer $dataLayer) {
            $dataLayer->push('view_product', [
                'ecommerce' => [
                    'items' => [[
                        'item_name' => '</script><script>alert(1)</script>',
                    ]],
                ],
            ]);

            return response('<html><head></head><body>ok</body></html>', 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        });

        $content = $this->get('/_marketing-data-layer-xss')
            ->assertOk()
            ->getContent();

        $this->assertIsString($content);
        $this->assertStringNotContainsString('</script><script>alert(1)</script>', $content);
        $this->assertStringContainsString('\\u003C/script\\u003E\\u003Cscript\\u003Ealert(1)\\u003C/script\\u003E', $content);
    }

    public function test_flash_push_survives_redirect_and_is_rendered_on_next_page(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        Route::middleware('web')->get('/_marketing-flash-source', function (MarketingDataLayer $dataLayer) {
            $dataLayer->flashPush('contact_form_sent', ['form' => 'contact']);

            return redirect('/_marketing-flash-target');
        });

        Route::middleware('web')->get('/_marketing-flash-target', fn () => response(
            '<html><head></head><body>ok</body></html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        ));

        $this->get('/_marketing-flash-source')->assertRedirect('/_marketing-flash-target');

        $this->get('/_marketing-flash-target')
            ->assertOk()
            ->assertSee('window.dataLayer.push({"event":"contact_form_sent","form":"contact"});', escape: false);
    }

    public function test_service_is_noop_when_marketing_tracking_is_disabled(): void
    {
        config()->set('marketing.tracking_enabled', false);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        Route::middleware('web')->get('/_marketing-disabled', function (MarketingDataLayer $dataLayer) {
            $dataLayer->push('view_product', ['value' => 100]);

            return response('<html><head></head><body>ok</body></html>', 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        });

        $this->get('/_marketing-disabled')
            ->assertOk()
            ->assertDontSee('view_product', escape: false)
            ->assertDontSee('googletagmanager.com/gtm.js', escape: false);
    }
}
