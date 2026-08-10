<?php

namespace App\Services;

use App\Models\Order;
use RuntimeException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class CheckoutPaymentIntentService
{
    public function prepare(Order $order): PaymentIntent
    {
        $order->refresh();

        if ($order->payment_status !== 'pending' || $order->stock_released_at) {
            throw new RuntimeException('Order is no longer payable.');
        }

        if (! $order->terms_accepted_at || ! $order->privacy_acknowledged_at || ! $order->terms_version) {
            throw new RuntimeException('Legal acceptance is required before creating a PaymentIntent.');
        }

        $stripe = new StripeClient((string) config('services.stripe.secret'));

        if ($order->stripe_transaction_id) {
            return $stripe->paymentIntents->retrieve($order->stripe_transaction_id);
        }

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => (int) round((float) $order->total_amount * 100),
            'currency' => 'ron',
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
            ],
            'description' => 'Comanda '.$order->order_number.' - '.config('shop.brand_name'),
            'metadata' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ],
        ], [
            'idempotency_key' => 'mtd-payment-intent-'.$order->id,
        ]);

        Order::query()
            ->whereKey($order->id)
            ->whereNull('stripe_transaction_id')
            ->update(['stripe_transaction_id' => $paymentIntent->id]);

        return $paymentIntent;
    }
}
