<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductViewTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_pushes_view_product_event_with_ecommerce_payload(): void
    {
        config()->set('marketing.tracking_enabled', true);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        $category = Category::create([
            'name' => ['ro' => 'Cruci'],
            'slug' => 'cruci',
            'description' => ['ro' => 'Categorie test'],
        ]);

        $product = Product::create([
            'product_code' => 'CRUCE-001',
            'product_type' => 'unicat',
            'category_id' => $category->id,
            'name' => ['ro' => 'Cruce din lemn și rășină'],
            'slug' => 'cruce-test',
            'description' => ['ro' => 'Produs test'],
            'price' => 249.50,
            'stock' => 1,
            'is_custom' => false,
            'status' => 'published',
        ]);

        $content = $this->get(route('shop.show', $product->slug))
            ->assertOk()
            ->getContent();

        $this->assertIsString($content);
        $this->assertStringContainsString('window.dataLayer.push({"event":"view_product"', $content);
        $this->assertStringContainsString('"currency":"RON"', $content);
        $this->assertStringContainsString('"value":249.5', $content);
        $this->assertStringContainsString('"item_id":"CRUCE-001"', $content);
        $this->assertStringContainsString('"item_name":"Cruce din lemn și rășină"', $content);
        $this->assertStringContainsString('"item_category":"Cruci"', $content);
        $this->assertStringContainsString('"item_variant":"unicat"', $content);
        $this->assertStringContainsString('"price":249.5', $content);
        $this->assertStringNotContainsString('customer_email', $content);
        $this->assertStringNotContainsString('customer_phone', $content);
    }

    public function test_product_page_does_not_push_view_product_when_tracking_is_disabled(): void
    {
        config()->set('marketing.tracking_enabled', false);
        config()->set('marketing.gtm.container_id', 'GTM-TEST123');

        $category = Category::create([
            'name' => ['ro' => 'Cruci'],
            'slug' => 'cruci',
            'description' => ['ro' => 'Categorie test'],
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => ['ro' => 'Cruce test'],
            'slug' => 'cruce-test-disabled',
            'description' => ['ro' => 'Produs test'],
            'price' => 100,
            'stock' => 1,
            'is_custom' => false,
            'status' => 'published',
        ]);

        $this->get(route('shop.show', $product->slug))
            ->assertOk()
            ->assertDontSee('"event":"view_product"', escape: false);
    }
}
