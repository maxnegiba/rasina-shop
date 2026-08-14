<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRelistForSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_sold_product_can_be_relisted_without_changing_its_commercial_identity(): void
    {
        $category = Category::query()->create([
            'name' => ['ro' => 'Test'],
            'slug' => 'test-relist',
        ]);

        $product = Product::query()->create([
            'product_code' => 'TEST-REL-001',
            'name' => ['ro' => 'Piesă test'],
            'slug' => 'piesa-test-relist',
            'category_id' => $category->id,
            'price' => 250,
            'stock' => 0,
            'is_custom' => true,
            'status' => 'published',
            'image' => 'products/test.webp',
        ]);

        $product->relistForSale();

        $this->assertSame(1, (int) $product->stock);
        $this->assertSame('published', $product->status);
        $this->assertSame('250.00', $product->price);
        $this->assertTrue($product->is_custom);
        $this->assertSame($category->id, $product->category_id);
        $this->assertTrue($product->isPurchasable());
    }

    public function test_relisting_a_draft_sold_product_publishes_it_again(): void
    {
        $category = Category::query()->create([
            'name' => ['ro' => 'Test'],
            'slug' => 'test-relist-draft',
        ]);

        $product = Product::query()->create([
            'product_code' => 'TEST-REL-002',
            'name' => ['ro' => 'Piesă draft'],
            'slug' => 'piesa-draft-relist',
            'category_id' => $category->id,
            'price' => 100,
            'stock' => 0,
            'is_custom' => true,
            'status' => 'draft',
            'image' => 'products/test.webp',
        ]);

        $product->relistForSale();

        $this->assertSame(1, (int) $product->stock);
        $this->assertSame('published', $product->status);
    }

    public function test_product_without_valid_price_cannot_be_relisted(): void
    {
        $category = Category::query()->create([
            'name' => ['ro' => 'Test'],
            'slug' => 'test-relist-invalid',
        ]);

        $product = Product::query()->create([
            'product_code' => 'TEST-REL-003',
            'name' => ['ro' => 'Piesă invalidă'],
            'slug' => 'piesa-invalida-relist',
            'category_id' => $category->id,
            'price' => null,
            'stock' => 0,
            'is_custom' => true,
            'status' => 'published',
            'image' => 'products/test.webp',
        ]);

        try {
            $product->relistForSale();
            $this->fail('Expected relisting to reject a product without a valid price.');
        } catch (\LogicException $exception) {
            $this->assertStringContainsString('preț valid', $exception->getMessage());
        }

        $product->refresh();
        $this->assertSame(0, (int) $product->stock);
    }
}
