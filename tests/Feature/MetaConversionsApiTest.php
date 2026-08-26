<?php

namespace Tests\Feature;

use App\Jobs\SendMetaPurchase;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class MetaConversionsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_job_sends_deduplicated_purchase_payload_without_customer_pii(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.meta.pixel_id', '1397295259019403');
        config()->set('marketing.meta.capi_access_token', 'test-token');
        config()->set('marketing.meta.graph_api_version', 'v23.0');
        config()->set('marketing.meta.test_event_code', null);

        Http::fake([
            'graph.facebook.com/*' => Http::response(['events_received' => 1], 200),
        ]);

        $category = Category::create([
            'name' => ['ro' => 'Meta test'],
            'slug' => 'meta-test-'.Str::random(6),
        ]);

        $product = Product::create([
            'product_code' => 'META-001',
            'product_type' => 'unicat',
            'category_id' => $category->id,
            'name' => ['ro' => 'Produs Meta'],
            'slug' => 'produs-meta-'.Str::random(6),
            'price' => 90,
            'stock' => 1,
            'status' => 'published',
        ]);

        $order = Order::create([
            'order_number' => 'MTD-META-'.strtoupper(bin2hex(random_bytes(3))),
            'total_amount' => 100,
            'shipping_amount' => 10,
            'payment_status' => 'paid',
            'payment_method' => 'stripe',
            'shipping_status' => 'processing',
            'customer_details' => [
                'email' => 'never-send@example.test',
                'phone' => '0712345678',
            ],
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Produs Meta',
            'product_code' => 'META-001',
            'quantity' => 1,
            'unit_price' => 90,
        ]);

        $job = new SendMetaPurchase(
            orderId: $order->id,
            eventId: 'mtd-purchase-'.$order->order_number,
            eventTime: 1787240000,
            eventSourceUrl: 'https://mtdart.ro/checkout/success?order=test',
            userData: [
                'client_ip_address' => '203.0.113.10',
                'client_user_agent' => 'Test Browser',
                'fbp' => 'fb.1.test',
                'fbc' => null,
            ],
        );

        $job->handle(app(\App\Services\MetaConversionsApi::class));

        Http::assertSent(function (Request $request) use ($order): bool {
            $payload = $request->data();
            $event = data_get($payload, 'data.0', []);
            $encoded = json_encode($payload);

            return $request->url() === 'https://graph.facebook.com/v23.0/1397295259019403/events'
                && $request->hasHeader('Authorization', 'Bearer test-token')
                && ! array_key_exists('test_event_code', $payload)
                && data_get($event, 'event_name') === 'Purchase'
                && data_get($event, 'event_id') === 'mtd-purchase-'.$order->order_number
                && data_get($event, 'action_source') === 'website'
                && data_get($event, 'custom_data.currency') === 'RON'
                && (float) data_get($event, 'custom_data.value') === 90.0
                && data_get($event, 'custom_data.order_id') === $order->order_number
                && data_get($event, 'custom_data.contents.0.id') === 'META-001'
                && data_get($event, 'user_data.client_ip_address') === '203.0.113.10'
                && data_get($event, 'user_data.client_user_agent') === 'Test Browser'
                && data_get($event, 'user_data.fbp') === 'fb.1.test'
                && ! str_contains((string) $encoded, 'never-send@example.test')
                && ! str_contains((string) $encoded, '0712345678');
        });
    }

    public function test_client_includes_test_event_code_when_configured(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.meta.pixel_id', '1397295259019403');
        config()->set('marketing.meta.capi_access_token', 'test-token');
        config()->set('marketing.meta.graph_api_version', 'v23.0');
        config()->set('marketing.meta.test_event_code', 'TEST83946');

        Http::fake([
            'graph.facebook.com/*' => Http::response(['events_received' => 1], 200),
        ]);

        app(\App\Services\MetaConversionsApi::class)->sendPurchase([
            'event_name' => 'Purchase',
            'event_time' => 1787240000,
            'event_id' => 'mtd-purchase-test',
            'action_source' => 'website',
            'user_data' => [],
            'custom_data' => [
                'currency' => 'RON',
                'value' => 90,
            ],
        ]);

        Http::assertSent(fn (Request $request): bool =>
            data_get($request->data(), 'test_event_code') === 'TEST83946'
        );
    }

    public function test_client_is_noop_when_tracking_or_credentials_are_disabled(): void
    {
        config()->set('marketing.tracking_enabled', false);
        config()->set('marketing.meta.pixel_id', null);
        config()->set('marketing.meta.capi_access_token', null);

        Http::fake();

        app(\App\Services\MetaConversionsApi::class)->sendPurchase([
            'event_name' => 'Purchase',
        ]);

        Http::assertNothingSent();
    }
}
