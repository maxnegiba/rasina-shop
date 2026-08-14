<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PurgeTestOrdersCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_mode_does_not_delete_orders(): void
    {
        $order = $this->order('pending');

        $this->artisan('orders:purge-test')
            ->assertSuccessful();

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_execute_mode_refuses_an_incorrect_confirmation_phrase(): void
    {
        $order = $this->order('pending');

        $this->artisan('orders:purge-test', [
            '--execute' => true,
            '--confirm' => 'WRONG',
        ])->assertFailed();

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_cleanup_deletes_orders_and_restores_only_unreleased_reserved_stock(): void
    {
        $productId = $this->productWithCurrentStock(7);

        $unreleased = $this->order('paid', [
            'stock_reserved_at' => now()->subMinutes(10),
            'stock_released_at' => null,
        ]);
        $unreleased->items()->create([
            'product_id' => $productId,
            'product_name' => 'Produs test A',
            'quantity' => 2,
            'unit_price' => 50,
        ]);

        $alreadyReleased = $this->order('failed', [
            'stock_reserved_at' => now()->subMinutes(10),
            'stock_released_at' => now()->subMinutes(5),
        ]);
        $alreadyReleased->items()->create([
            'product_id' => $productId,
            'product_name' => 'Produs test B',
            'quantity' => 3,
            'unit_price' => 20,
        ]);

        $this->artisan('orders:purge-test', [
            '--execute' => true,
            '--confirm' => 'DELETE-ALL-TEST-ORDERS',
        ])->assertSuccessful();

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseHas('products', [
            'id' => $productId,
            'stock' => 9,
        ]);
        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseCount('products', 1);
    }

    private function order(string $paymentStatus, array $overrides = []): Order
    {
        return Order::query()->create(array_merge([
            'order_number' => 'MTD-TEST-'.strtoupper(bin2hex(random_bytes(3))),
            'subtotal_amount' => 100,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 100,
            'payment_status' => $paymentStatus,
            'shipping_status' => 'processing',
            'customer_details' => [],
            'stock_reserved_at' => now(),
            'stock_released_at' => null,
        ], $overrides));
    }

    private function productWithCurrentStock(int $stock): int
    {
        $categoryId = DB::table('categories')->insertGetId([
            'name' => json_encode(['ro' => 'Test']),
            'slug' => 'test-'.strtolower(bin2hex(random_bytes(3))),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('products')->insertGetId([
            'category_id' => $categoryId,
            'name' => json_encode(['ro' => 'Produs test']),
            'slug' => 'produs-test-'.strtolower(bin2hex(random_bytes(3))),
            'price' => 100,
            'stock' => $stock,
            'is_custom' => false,
            'status' => 'published',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
