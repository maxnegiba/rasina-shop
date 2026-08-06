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
            ->assertJson(['success' => true, 'cart_count' => 1]);

        $this->assertSame(1, session('cart')[$product->id]['quantity']);
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

    private function product(int $stock): Product
    {
        $category = Category::create([
            'name' => ['ro' => 'Test'],
            'slug' => 'test-'.Str::random(8),
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => ['ro' => 'Produs test'],
            'slug' => 'produs-test-'.Str::random(8),
            'price' => 100,
            'stock' => $stock,
            'status' => 'published',
        ]);
    }
}
