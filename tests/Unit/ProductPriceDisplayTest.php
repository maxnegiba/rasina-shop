<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductPriceDisplayTest extends TestCase
{
    public function test_a_unique_product_can_display_its_price(): void
    {
        $product = new Product([
            'is_custom' => true,
            'price' => 1250,
        ]);

        $this->assertSame('1.250 RON', $product->displayPrice());
    }

    public function test_a_product_without_a_price_keeps_the_request_price_fallback(): void
    {
        $product = new Product([
            'is_custom' => true,
            'price' => null,
        ]);

        $this->assertSame('Preț la cerere', $product->displayPrice());
    }

    public function test_a_unique_product_uses_the_normal_purchase_flow_when_priced_and_in_stock(): void
    {
        $product = new Product([
            'is_custom' => true,
            'price' => 1250,
            'stock' => 1,
        ]);

        $this->assertTrue($product->isPurchasable());
    }

    public function test_a_unique_product_without_a_valid_price_cannot_enter_checkout(): void
    {
        $product = new Product([
            'is_custom' => true,
            'price' => null,
            'stock' => 1,
        ]);

        $this->assertFalse($product->isPurchasable());
    }

    public function test_a_unique_product_without_stock_cannot_enter_checkout(): void
    {
        $product = new Product([
            'is_custom' => true,
            'price' => 1250,
            'stock' => 0,
        ]);

        $this->assertFalse($product->isPurchasable());
    }
}
