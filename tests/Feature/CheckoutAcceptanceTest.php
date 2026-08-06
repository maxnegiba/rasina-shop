<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_records_both_required_acceptances(): void
    {
        $order = $this->pendingOrder();

        $this->withSession(['checkout_order_token' => $order->public_token])
            ->postJson(route('checkout.accept-terms'), [
                'order_token' => $order->public_token,
                'accept_terms' => true,
                'acknowledge_privacy' => true,
            ])
            ->assertOk()
            ->assertJson(['accepted' => true]);

        $order->refresh();

        $this->assertNotNull($order->terms_accepted_at);
        $this->assertNotNull($order->privacy_acknowledged_at);
        $this->assertSame(config('shop.terms_version'), $order->terms_version);
    }

    public function test_checkout_rejects_missing_acceptance(): void
    {
        $order = $this->pendingOrder();

        $this->withSession(['checkout_order_token' => $order->public_token])
            ->postJson(route('checkout.accept-terms'), [
                'order_token' => $order->public_token,
                'accept_terms' => true,
                'acknowledge_privacy' => false,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('acknowledge_privacy');

        $this->assertNull($order->fresh()->terms_accepted_at);
        $this->assertNull($order->fresh()->privacy_acknowledged_at);
    }

    private function pendingOrder(): Order
    {
        return Order::create([
            'order_number' => 'MTD-TEST-'.strtoupper(bin2hex(random_bytes(3))),
            'total_amount' => 100,
            'payment_status' => 'pending',
            'shipping_status' => 'processing',
            'customer_details' => [],
            'stock_reserved_at' => now(),
        ]);
    }
}
