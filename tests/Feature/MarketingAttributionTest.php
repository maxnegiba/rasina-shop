<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\MarketingAttribution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MarketingAttributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_utm_touch_is_captured_only_with_marketing_consent(): void
    {
        Route::middleware('web')->get('/_attribution-capture-test', fn () => response('ok'));

        $url = '/_attribution-capture-test?utm_source=facebook&utm_medium=paid_social&utm_campaign=cruci_august&utm_content=video_02';

        $withoutMarketing = $this
            ->withUnencryptedCookie('__cookie_consent', '2')
            ->get($url)
            ->assertOk();

        $withoutMarketingCookie = collect($withoutMarketing->headers->getCookies())
            ->first(fn ($cookie): bool => $cookie->getName() === MarketingAttribution::COOKIE_NAME);
        $this->assertNull($withoutMarketingCookie);

        $withMarketing = $this
            ->withUnencryptedCookie('__cookie_consent', 'true')
            ->get($url)
            ->assertOk();

        $cookie = collect($withMarketing->headers->getCookies())
            ->first(fn ($cookie): bool => $cookie->getName() === MarketingAttribution::COOKIE_NAME);

        $this->assertNotNull($cookie);
        $payload = json_decode((string) $cookie->getValue(), true);
        $this->assertSame('facebook', data_get($payload, 'last_touch.utm_source'));
        $this->assertSame('paid_social', data_get($payload, 'last_touch.utm_medium'));
        $this->assertSame('cruci_august', data_get($payload, 'last_touch.utm_campaign'));
        $this->assertSame('video_02', data_get($payload, 'last_touch.utm_content'));
    }

    public function test_new_order_persists_last_touch_and_first_touch_attribution(): void
    {
        Route::middleware('web')->get('/_attribution-order-test', function () {
            $order = Order::create([
                'order_number' => 'MTD-ATTR-'.strtoupper(bin2hex(random_bytes(3))),
                'subtotal_amount' => 90,
                'shipping_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 90,
                'payment_status' => 'pending',
                'payment_method' => 'cash_on_delivery',
                'shipping_status' => 'processing',
                'customer_details' => [],
            ]);

            return response()->json(['id' => $order->id]);
        });

        $cookie = json_encode([
            'version' => 1,
            'first_touch' => [
                'utm_source' => 'facebook',
                'utm_medium' => 'paid_social',
                'utm_campaign' => 'launch',
                'landing_path' => '/magazin',
                'captured_at' => '2026-08-21T08:00:00+00:00',
            ],
            'last_touch' => [
                'utm_source' => 'instagram',
                'utm_medium' => 'social',
                'utm_campaign' => 'retargeting',
                'utm_content' => 'reel_03',
                'landing_path' => '/magazin/produs/test',
                'captured_at' => '2026-08-21T09:00:00+00:00',
            ],
        ]);

        $response = $this
            ->withUnencryptedCookie('__cookie_consent', 'true')
            ->withUnencryptedCookie(MarketingAttribution::COOKIE_NAME, $cookie)
            ->get('/_attribution-order-test')
            ->assertOk();

        $order = Order::findOrFail($response->json('id'));

        $this->assertSame('instagram', $order->utm_source);
        $this->assertSame('social', $order->utm_medium);
        $this->assertSame('retargeting', $order->utm_campaign);
        $this->assertSame('reel_03', $order->utm_content);
        $this->assertSame('facebook', data_get($order->marketing_attribution, 'first_touch.utm_source'));
        $this->assertSame('instagram', data_get($order->marketing_attribution, 'last_touch.utm_source'));
    }
}
