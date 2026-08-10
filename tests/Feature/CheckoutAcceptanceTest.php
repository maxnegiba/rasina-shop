<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\CheckoutPaymentIntentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Stripe\PaymentIntent;
use Tests\TestCase;

class CheckoutAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_records_both_required_acceptances_before_returning_client_secret(): void
    {
        $order = $this->pendingOrder();
        $intent = PaymentIntent::constructFrom([
            'id' => 'pi_test_acceptance',
            'client_secret' => 'pi_test_acceptance_secret',
        ]);

        $service = Mockery::mock(CheckoutPaymentIntentService::class);
        $service->shouldReceive('prepare')
            ->once()
            ->with(Mockery::on(function (Order $candidate) use ($order): bool {
                $candidate->refresh();

                return $candidate->is($order)
                    && $candidate->terms_accepted_at !== null
                    && $candidate->privacy_acknowledged_at !== null
                    && $candidate->terms_version === config('shop.terms_version');
            }))
            ->andReturn($intent);
        $this->app->instance(CheckoutPaymentIntentService::class, $service);

        $this->withSession(['checkout_order_token' => $order->public_token])
            ->postJson(route('checkout.accept-terms'), [
                'order_token' => $order->public_token,
                'accept_terms' => true,
                'acknowledge_privacy' => true,
            ])
            ->assertOk()
            ->assertJson([
                'accepted' => true,
                'client_secret' => 'pi_test_acceptance_secret',
            ]);

        $order->refresh();

        $this->assertNotNull($order->terms_accepted_at);
        $this->assertNotNull($order->privacy_acknowledged_at);
        $this->assertSame(config('shop.terms_version'), $order->terms_version);
    }

    public function test_checkout_rejects_missing_acceptance_without_creating_payment_intent(): void
    {
        $order = $this->pendingOrder();

        $service = Mockery::mock(CheckoutPaymentIntentService::class);
        $service->shouldNotReceive('prepare');
        $this->app->instance(CheckoutPaymentIntentService::class, $service);

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

    public function test_payment_intent_service_refuses_order_without_recorded_legal_acceptance(): void
    {
        $order = $this->pendingOrder();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Legal acceptance is required');

        app(CheckoutPaymentIntentService::class)->prepare($order);
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
