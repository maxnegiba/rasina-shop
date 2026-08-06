<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmationMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderPaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_checkout_is_fulfilled_only_once(): void
    {
        Mail::fake();
        [$order, $product] = $this->reservedOrder();
        $checkoutSession = $this->paidCheckoutSession($order);
        $service = app(OrderPaymentService::class);

        $service->completeCheckout($checkoutSession);
        $service->completeCheckout($checkoutSession);

        $order->refresh();
        $product->refresh();

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('pi_test_123', $order->stripe_transaction_id);
        $this->assertSame('PROFORMA-'.now()->format('Y').'-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT), $order->proforma_number);
        $this->assertSame(0, $product->stock);
        Mail::assertSent(OrderConfirmationMail::class, 1);
    }

    public function test_expired_checkout_releases_reserved_stock_only_once(): void
    {
        [$order, $product] = $this->reservedOrder();
        $checkoutSession = (object) [
            'id' => $order->stripe_checkout_session_id,
            'client_reference_id' => (string) $order->id,
            'metadata' => (object) ['order_id' => (string) $order->id],
        ];
        $service = app(OrderPaymentService::class);

        $service->expireCheckout($checkoutSession);
        $service->expireCheckout($checkoutSession);

        $this->assertSame('failed', $order->fresh()->payment_status);
        $this->assertNotNull($order->fresh()->stock_released_at);
        $this->assertSame(1, $product->fresh()->stock);
    }

    private function reservedOrder(): array
    {
        $category = Category::create([
            'name' => ['ro' => 'Test'],
            'slug' => 'test-'.uniqid(),
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => ['ro' => 'Produs test'],
            'slug' => 'produs-test-'.uniqid(),
            'price' => 100,
            'stock' => 0,
            'status' => 'published',
        ]);
        $order = Order::create([
            'order_number' => 'MTD-TEST-'.strtoupper(bin2hex(random_bytes(3))),
            'total_amount' => 100,
            'payment_status' => 'pending',
            'shipping_status' => 'processing',
            'customer_details' => [],
            'stripe_checkout_session_id' => 'cs_test_'.bin2hex(random_bytes(5)),
            'stock_reserved_at' => now(),
            'terms_accepted_at' => now(),
            'terms_version' => config('shop.terms_version'),
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 100,
        ]);

        return [$order, $product];
    }

    private function paidCheckoutSession(Order $order): object
    {
        $address = (object) [
            'line1' => 'Str. Test nr. 1',
            'line2' => null,
            'city' => 'București',
            'state' => 'București',
            'postal_code' => '010101',
            'country' => 'RO',
        ];

        return (object) [
            'id' => $order->stripe_checkout_session_id,
            'payment_status' => 'paid',
            'amount_total' => 10000,
            'currency' => 'ron',
            'client_reference_id' => (string) $order->id,
            'metadata' => (object) ['order_id' => (string) $order->id],
            'payment_intent' => 'pi_test_123',
            'customer_details' => (object) [
                'name' => 'Client Test',
                'email' => 'client@example.com',
                'phone' => '0700000000',
                'address' => $address,
            ],
            'shipping_details' => (object) [
                'name' => 'Client Test',
                'phone' => '0700000000',
                'address' => $address,
            ],
        ];
    }
}
