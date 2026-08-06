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

    public function test_succeeded_payment_intent_is_fulfilled_only_once(): void
    {
        Mail::fake();
        [$order, $product] = $this->reservedOrder();
        $paymentIntent = $this->succeededPaymentIntent($order);
        $service = app(OrderPaymentService::class);

        $service->completePaymentIntent($paymentIntent);
        $service->completePaymentIntent($paymentIntent);

        $order->refresh();
        $product->refresh();

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('pi_test_123', $order->stripe_transaction_id);
        $this->assertSame('PROFORMA-'.now()->format('Y').'-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT), $order->proforma_number);
        $this->assertSame(0, $product->stock);
        Mail::assertQueued(OrderConfirmationMail::class, 1);
    }

    public function test_canceled_payment_intent_releases_reserved_stock_only_once(): void
    {
        [$order, $product] = $this->reservedOrder();
        $paymentIntent = (object) [
            'id' => $order->stripe_transaction_id,
            'metadata' => (object) ['order_id' => (string) $order->id],
        ];
        $service = app(OrderPaymentService::class);

        $service->cancelPayment($paymentIntent);
        $service->cancelPayment($paymentIntent);

        $this->assertSame('failed', $order->fresh()->payment_status);
        $this->assertNotNull($order->fresh()->stock_released_at);
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_failed_attempt_keeps_stock_reserved_for_retry(): void
    {
        [$order, $product] = $this->reservedOrder();
        $paymentIntent = (object) [
            'id' => $order->stripe_transaction_id,
            'metadata' => (object) ['order_id' => (string) $order->id],
        ];

        app(OrderPaymentService::class)->recordFailedAttempt($paymentIntent);

        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->assertNull($order->fresh()->stock_released_at);
        $this->assertSame(0, $product->fresh()->stock);
    }

    public function test_already_created_hosted_checkout_remains_fulfillable(): void
    {
        Mail::fake();
        [$order] = $this->reservedOrder();
        $order->update(['stripe_checkout_session_id' => 'cs_test_legacy']);
        $address = (object) [
            'line1' => 'Str. Test nr. 1',
            'line2' => null,
            'city' => 'București',
            'state' => 'București',
            'postal_code' => '010101',
            'country' => 'RO',
        ];
        $checkoutSession = (object) [
            'id' => 'cs_test_legacy',
            'payment_status' => 'paid',
            'amount_total' => 10000,
            'currency' => 'ron',
            'client_reference_id' => (string) $order->id,
            'metadata' => (object) ['order_id' => (string) $order->id],
            'payment_intent' => 'pi_test_legacy',
            'customer_details' => (object) [
                'email' => 'legacy@example.com',
                'phone' => '0700000000',
                'address' => $address,
            ],
            'shipping_details' => (object) [
                'name' => 'Client Legacy',
                'phone' => '0700000000',
                'address' => $address,
            ],
        ];

        app(OrderPaymentService::class)->completeLegacyCheckout($checkoutSession);

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('pi_test_legacy', $order->fresh()->stripe_transaction_id);
        Mail::assertQueued(OrderConfirmationMail::class, 1);
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
            'stripe_transaction_id' => 'pi_test_123',
            'stock_reserved_at' => now(),
            'terms_accepted_at' => now(),
            'privacy_acknowledged_at' => now(),
            'terms_version' => config('shop.terms_version'),
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 100,
        ]);

        return [$order, $product];
    }

    private function succeededPaymentIntent(Order $order): object
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
            'id' => $order->stripe_transaction_id,
            'status' => 'succeeded',
            'amount_received' => 10000,
            'currency' => 'ron',
            'metadata' => (object) ['order_id' => (string) $order->id],
            'receipt_email' => 'client@example.com',
            'shipping' => (object) [
                'name' => 'Client Test',
                'phone' => '0700000000',
                'address' => $address,
            ],
        ];
    }
}
