<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderItemSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_item_keeps_product_identity_after_product_deletion(): void
    {
        $category = Category::create([
            'name' => ['ro' => 'Test'],
            'slug' => 'test-'.Str::random(8),
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'product_code' => 'CR-TEST',
            'name' => ['ro' => 'Cruce turcoaz'],
            'slug' => 'cruce-'.Str::random(8),
            'price' => 250,
            'stock' => 1,
            'status' => 'published',
        ]);
        $order = Order::create([
            'order_number' => 'MTD-TEST-'.Str::random(8),
            'subtotal_amount' => 250,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 250,
            'payment_status' => 'paid',
            'shipping_status' => 'processing',
            'customer_details' => [],
        ]);
        $item = $order->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Cruce turcoaz',
            'product_code' => 'CR-TEST',
            'quantity' => 1,
            'unit_price' => 250,
        ]);

        $product->delete();

        $this->assertNull($item->fresh()->product_id);
        $this->assertSame('Cruce turcoaz', $item->fresh()->displayName());
        $this->assertSame('CR-TEST', $item->fresh()->product_code);
    }
}
