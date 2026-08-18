<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_add_request_cannot_add_the_product_twice(): void
    {
        $product = $this->product(stock: 2);
        $requestToken = (string) Str::uuid();
        $payload = [
            'product_id' => $product->id,
            'quantity' => 1,
            'request_token' => $requestToken,
        ];

        $this->postJson(route('cart.add'), $payload)
            ->assertOk()
            ->assertJson(['success' => true, 'cart_count' => 1]);

        $this->postJson(route('cart.add'), $payload)
            ->assertOk()
            ->assertJson([
                'success' => true,
                'cart_count' => 1,
                'marketing_event' => null,
            ]);

        $this->assertSame(1, session('cart')[$product->id]['quantity']);
    }

    public function test_successful_ajax_add_returns_a_standardized_add_to_cart_event(): void
    {
        config()->set('marketing.tracking_enabled', true);

        $product = $this->product(stock: 3, price: 125.50);
        $product->update([
            'product_code' => 'TEST-001',
            'product_type' => 'unicat',
        ]);

        $response = $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
            'request_token' => (string) Str::uuid(),
        ])->assertOk();

        $response->assertJsonPath('marketing_event.event', 'add_to_cart');
        $response->assertJsonPath('marketing_event.ecommerce.currency', 'RON');
        $response->assertJsonPath('marketing_event.ecommerce.value', 251);
        $response->assertJsonPath('marketing_event.ecommerce.items.0.item_id', 'TEST-001');
        $response->assertJsonPath('marketing_event.ecommerce.items.0.item_name', 'Produs test');
        $response->assertJsonPath('marketing_event.ecommerce.items.0.item_category', 'Test');
        $response->assertJsonPath('marketing_event.ecommerce.items.0.item_variant', 'unicat');
        $response->assertJsonPath('marketing_event.ecommerce.items.0.price', 125.5);
        $response->assertJsonPath('marketing_event.ecommerce.items.0.quantity', 2);
    }

    public function test_add_to_cart_event_is_not_returned_when_tracking_is_disabled(): void
    {
        config()->set('marketing.tracking_enabled', false);

        $product = $this->product(stock: 1);

        $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk()
            ->assertJsonPath('marketing_event', null);
    }

    public function test_redirecting_add_flashes_event_for_the_next_page_instead_of_returning_it_twice(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        $product = $this->product(stock: 1);

        $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
            'redirect_to_checkout' => true,
        ])->assertOk()
            ->assertJsonPath('marketing_event', null)
            ->assertJsonPath('redirect_url', route('checkout.index'));

        $content = $this->get(route('checkout.index'))->getContent();

        $this->assertIsString($content);
        $this->assertStringContainsString('window.dataLayer.push({"event":"add_to_cart"', $content);
    }

    public function test_cart_quantity_can_be_updated_and_survives_navigation(): void
    {
        $product = $this->product(stock: 3);

        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();

        $this->postJson(route('cart.update'), [
            'id' => $product->id,
            'quantity' => 3,
        ])->assertOk()->assertJson(['cart_count' => 3]);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Coș de cumpărături')
            ->assertSee('value="3"', false);

        $this->assertSame(3, session('cart')[$product->id]['quantity']);
    }

    public function test_cart_rejects_a_quantity_above_current_stock(): void
    {
        $product = $this->product(stock: 1);

        $this->withSession([
            'cart' => [
                $product->id => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'quantity' => 1,
                    'price' => 100.0,
                    'image' => '/img/logo.png',
                    'stock' => 1,
                ],
            ],
        ])->postJson(route('cart.update'), [
            'id' => $product->id,
            'quantity' => 2,
        ])->assertUnprocessable();

        $this->assertSame(1, session('cart')[$product->id]['quantity']);
    }

    private function product(int $stock, float $price = 100): Product
    {
        $category = Category::create([
            'name' => ['ro' => 'Test'],
            'slug' => 'test-'.Str::random(8),
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => ['ro' => 'Produs test'],
            'slug' => 'produs-test-'.Str::random(8),
            'price' => $price,
            'stock' => $stock,
            'status' => 'published',
        ]);
    }
}
