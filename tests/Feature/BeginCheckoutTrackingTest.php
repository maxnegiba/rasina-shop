<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackBeginCheckout;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class BeginCheckoutTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_checkout_render_pushes_begin_checkout_with_all_cart_items(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        [$first, $second] = $this->products();

        Route::middleware(['web', TrackBeginCheckout::class])->get('/_begin-checkout-test', fn () => response(
            '<html><head></head><body>checkout</body></html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        ));

        $cart = [
            $first->id => [
                'id' => $first->id,
                'name' => $first->name,
                'slug' => $first->slug,
                'quantity' => 2,
                'price' => 125.50,
                'image' => '/img/logo.png',
                'stock' => 3,
            ],
            $second->id => [
                'id' => $second->id,
                'name' => $second->name,
                'slug' => $second->slug,
                'quantity' => 1,
                'price' => 49.00,
                'image' => '/img/logo.png',
                'stock' => 2,
            ],
        ];

        $content = $this->withSession(['cart' => $cart])
            ->get('/_begin-checkout-test')
            ->assertOk()
            ->getContent();

        $this->assertIsString($content);
        $this->assertStringContainsString('window.dataLayer.push({"event":"begin_checkout"', $content);
        $this->assertStringContainsString('"currency":"RON"', $content);
        $this->assertStringContainsString('"value":300', $content);
        $this->assertStringContainsString('"item_id":"CHECKOUT-001"', $content);
        $this->assertStringContainsString('"item_id":"CHECKOUT-002"', $content);
        $this->assertStringContainsString('"item_category":"Checkout test"', $content);
        $this->assertStringContainsString('"quantity":2', $content);
        $this->assertStringContainsString('"quantity":1', $content);
        $this->assertStringNotContainsString('customer_email', $content);
        $this->assertStringNotContainsString('customer_phone', $content);
    }

    public function test_begin_checkout_is_not_emitted_twice_for_same_checkout_and_cart(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        [$product] = $this->products();

        Route::middleware(['web', TrackBeginCheckout::class])->get('/_begin-checkout-dedup', fn () => response(
            '<html><head></head><body>checkout</body></html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        ));

        $cart = [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'quantity' => 1,
                'price' => 125.50,
                'image' => '/img/logo.png',
                'stock' => 3,
            ],
        ];

        $this->withSession([
            'cart' => $cart,
            'checkout_order_token' => '00000000-0000-4000-8000-000000000001',
        ])->get('/_begin-checkout-dedup')
            ->assertOk()
            ->assertSee('"event":"begin_checkout"', escape: false);

        $this->get('/_begin-checkout-dedup')
            ->assertOk()
            ->assertDontSee('"event":"begin_checkout"', escape: false);
    }

    public function test_begin_checkout_is_not_emitted_for_non_success_response_or_disabled_tracking(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        [$product] = $this->products();
        $cart = [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'quantity' => 1,
                'price' => 125.50,
                'image' => '/img/logo.png',
                'stock' => 3,
            ],
        ];

        Route::middleware(['web', TrackBeginCheckout::class])->get('/_begin-checkout-failed', fn () => response(
            '<html><head></head><body>failed</body></html>',
            502,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        ));

        $this->withSession(['cart' => $cart])
            ->get('/_begin-checkout-failed')
            ->assertStatus(502)
            ->assertDontSee('"event":"begin_checkout"', escape: false);

        config()->set('marketing.tracking_enabled', false);

        Route::middleware(['web', TrackBeginCheckout::class])->get('/_begin-checkout-disabled', fn () => response(
            '<html><head></head><body>checkout</body></html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        ));

        $this->withSession(['cart' => $cart])
            ->get('/_begin-checkout-disabled')
            ->assertOk()
            ->assertDontSee('"event":"begin_checkout"', escape: false);
    }

    private function products(): array
    {
        $category = Category::create([
            'name' => ['ro' => 'Checkout test'],
            'slug' => 'checkout-test',
        ]);

        $first = Product::create([
            'product_code' => 'CHECKOUT-001',
            'product_type' => 'unicat',
            'category_id' => $category->id,
            'name' => ['ro' => 'Produs checkout unu'],
            'slug' => 'produs-checkout-unu',
            'price' => 125.50,
            'stock' => 3,
            'status' => 'published',
        ]);

        $second = Product::create([
            'product_code' => 'CHECKOUT-002',
            'product_type' => 'serie_mica',
            'category_id' => $category->id,
            'name' => ['ro' => 'Produs checkout doi'],
            'slug' => 'produs-checkout-doi',
            'price' => 49.00,
            'stock' => 2,
            'status' => 'published',
        ]);

        return [$first, $second];
    }
}
