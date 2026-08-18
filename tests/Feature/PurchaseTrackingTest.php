<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackPurchase;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\MarketingDataLayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class PurchaseTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_on_delivery_success_page_pushes_purchase_without_pii(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        [$order, $product] = $this->cashOnDeliveryOrder();

        $content = $this->withSession([
            'checkout_order_token' => $order->public_token,
            'cart' => [
                $product->id => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => 1,
                    'price' => 90,
                ],
            ],
        ])->get(route('checkout.success', [
            'order' => $order->public_token,
            'method' => 'cod',
        ]))->assertOk()->getContent();

        $this->assertIsString($content);
        $this->assertStringContainsString('window.dataLayer.push({"event":"purchase"', $content);
        $this->assertStringContainsString('"transaction_id":"'.$order->order_number.'"', $content);
        $this->assertStringContainsString('"currency":"RON"', $content);
        $this->assertStringContainsString('"value":90', $content);
        $this->assertStringContainsString('"shipping":0', $content);
        $this->assertStringContainsString('"item_id":"PURCHASE-001"', $content);
        $this->assertStringContainsString('"item_name":"Produs purchase"', $content);
        $this->assertStringContainsString('"price":90', $content);
        $this->assertStringContainsString('"quantity":1', $content);
        $this->assertStringNotContainsString('cumparator@example.test', $content);
        $this->assertStringNotContainsString('0712345678', $content);
    }

    public function test_same_transaction_is_pushed_only_once_per_session(): void
    {
        config()->set('marketing.tracking_enabled', true);

        [$order] = $this->cashOnDeliveryOrder();
        $event = [
            'event' => 'purchase',
            'ecommerce' => [
                'transaction_id' => $order->order_number,
                'currency' => 'RON',
                'value' => 90.0,
                'shipping' => 0.0,
                'items' => [],
            ],
        ];

        $dataLayer = Mockery::mock(MarketingDataLayer::class);
        $dataLayer->shouldReceive('purchaseEvent')
            ->twice()
            ->andReturn($event);
        $dataLayer->shouldReceive('push')
            ->once()
            ->with('purchase', ['ecommerce' => $event['ecommerce']]);
        $this->app->instance(MarketingDataLayer::class, $dataLayer);

        Route::middleware(['web', TrackPurchase::class])->get('/_purchase-dedup-test', fn () => response(
            '<html><head></head><body>purchase</body></html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        ));

        $url = '/_purchase-dedup-test?order='.$order->public_token.'&method=cod';

        $this->get($url)->assertOk();
        $this->get($url)->assertOk();

        $this->assertContains(
            $order->order_number,
            session('marketing_purchase_transaction_ids', []),
        );
    }

    public function test_pending_stripe_or_invalid_success_page_does_not_push_purchase(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        [$order] = $this->cashOnDeliveryOrder();
        $order->update([
            'payment_method' => 'stripe',
            'payment_status' => 'pending',
            'stripe_transaction_id' => 'pi_pending_test',
        ]);

        Route::middleware(['web', TrackPurchase::class])->get('/_purchase-invalid-test', fn () => response(
            '<html><head></head><body>pending</body></html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        ));

        $content = $this->get('/_purchase-invalid-test?order='.$order->public_token)
            ->assertOk()
            ->getContent();

        $this->assertIsString($content);
        $this->assertStringNotContainsString('"event":"purchase"', $content);
    }

    public function test_purchase_event_is_disabled_by_emergency_switch(): void
    {
        config()->set('marketing.tracking_enabled', false);

        [$order] = $this->cashOnDeliveryOrder();

        $this->assertNull(app(MarketingDataLayer::class)->purchaseEvent($order));
    }

    private function cashOnDeliveryOrder(): array
    {
        $category = Category::create([
            'name' => ['ro' => 'Purchase test'],
            'slug' => 'purchase-test-'.Str::random(6),
        ]);

        $product = Product::create([
            'product_code' => 'PURCHASE-001',
            'product_type' => 'unicat',
            'category_id' => $category->id,
            'name' => ['ro' => 'Produs purchase'],
            'slug' => 'produs-purchase-'.Str::random(6),
            'price' => 90,
            'stock' => 1,
            'status' => 'published',
        ]);

        $order = Order::create([
            'order_number' => 'MTD-PURCHASE-'.strtoupper(bin2hex(random_bytes(3))),
            'total_amount' => 90,
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery',
            'shipping_status' => 'processing',
            'customer_details' => [
                'name' => 'Cumparator Test',
                'email' => 'cumparator@example.test',
                'phone' => '0712345678',
            ],
            'stock_reserved_at' => now(),
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Produs purchase',
            'product_code' => 'PURCHASE-001',
            'quantity' => 1,
            'unit_price' => 90,
        ]);

        return [$order->fresh(), $product];
    }
}
