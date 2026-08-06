<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class OrderPaymentService
{
    public function completePaymentIntent(object $paymentIntent): ?Order
    {
        if (($paymentIntent->status ?? null) !== 'succeeded') {
            return null;
        }

        $order = $this->findOrderForPaymentIntent($paymentIntent);

        if (! $order) {
            Log::error('Order not found for Stripe PaymentIntent.', [
                'payment_intent_id' => $paymentIntent->id ?? null,
            ]);

            return null;
        }

        return $this->fulfillOrder(
            order: $order,
            transactionId: (string) $paymentIntent->id,
            paidAmount: (int) ($paymentIntent->amount_received ?? $paymentIntent->amount ?? -1),
            currency: (string) ($paymentIntent->currency ?? ''),
            customerDetails: $this->paymentIntentCustomerDetails($paymentIntent),
        );
    }

    /**
     * Keeps already-created hosted Checkout Sessions fulfillable while new orders
     * use the embedded Payment Element again.
     */
    public function completeLegacyCheckout(object $checkoutSession): ?Order
    {
        if (($checkoutSession->payment_status ?? null) !== 'paid') {
            return null;
        }

        $order = $this->findOrderForCheckoutSession($checkoutSession);

        if (! $order) {
            Log::error('Order not found for legacy Stripe Checkout Session.', [
                'checkout_session_id' => $checkoutSession->id ?? null,
            ]);

            return null;
        }

        $paymentIntent = $checkoutSession->payment_intent ?? null;
        $transactionId = is_object($paymentIntent)
            ? ($paymentIntent->id ?? null)
            : $paymentIntent;

        if (! $transactionId) {
            throw new RuntimeException('Stripe Checkout Session has no PaymentIntent.');
        }

        return $this->fulfillOrder(
            order: $order,
            transactionId: (string) $transactionId,
            paidAmount: (int) ($checkoutSession->amount_total ?? -1),
            currency: (string) ($checkoutSession->currency ?? ''),
            customerDetails: $this->checkoutCustomerDetails($checkoutSession),
            checkoutSessionId: $checkoutSession->id ?? null,
        );
    }

    public function recordFailedAttempt(object $paymentIntent): void
    {
        $order = $this->findOrderForPaymentIntent($paymentIntent);

        if (! $order || $order->payment_status === 'paid') {
            return;
        }

        $order->update(['stripe_transaction_id' => $paymentIntent->id ?? $order->stripe_transaction_id]);
    }

    public function cancelPayment(object $paymentIntent): void
    {
        $order = $this->findOrderForPaymentIntent($paymentIntent);

        if (! $order || $order->payment_status === 'paid') {
            return;
        }

        $order->update([
            'payment_status' => 'failed',
            'stripe_transaction_id' => $paymentIntent->id ?? $order->stripe_transaction_id,
        ]);
        $order->releaseReservedStock();
    }

    public function expireLegacyCheckout(object $checkoutSession): void
    {
        $order = $this->findOrderForCheckoutSession($checkoutSession);

        if (! $order || $order->payment_status === 'paid') {
            return;
        }

        $order->update(['payment_status' => 'failed']);
        $order->releaseReservedStock();
    }

    private function fulfillOrder(
        Order $order,
        string $transactionId,
        int $paidAmount,
        string $currency,
        array $customerDetails,
        ?string $checkoutSessionId = null,
    ): Order {
        $expectedAmount = (int) round((float) $order->total_amount * 100);

        if ($paidAmount !== $expectedAmount || strtolower($currency) !== 'ron') {
            Log::critical('Stripe payment amount does not match the order.', [
                'order_id' => $order->id,
                'expected_amount' => $expectedAmount,
                'paid_amount' => $paidAmount,
                'currency' => $currency,
            ]);

            throw new RuntimeException('Stripe payment amount mismatch.');
        }

        $email = filter_var($customerDetails['email'] ?? null, FILTER_VALIDATE_EMAIL) ?: null;
        $mailClaimedAt = null;

        DB::transaction(function () use (
            $order,
            $transactionId,
            $checkoutSessionId,
            $customerDetails,
            $email,
            &$mailClaimedAt,
        ): void {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            $updates = [
                'payment_status' => 'paid',
                'customer_details' => $customerDetails,
                'stripe_transaction_id' => $transactionId,
            ];

            if ($checkoutSessionId) {
                $updates['stripe_checkout_session_id'] = $checkoutSessionId;
            }

            if (! $lockedOrder->proforma_number) {
                $updates['proforma_number'] = $lockedOrder->proformaNumber();
            }

            if ($email && ! $lockedOrder->confirmation_sent_at) {
                $mailClaimedAt = now();
                $updates['confirmation_sent_at'] = $mailClaimedAt;
            }

            $lockedOrder->update($updates);
        });

        $order->refresh()->load('items.product');

        if ($mailClaimedAt && $email) {
            try {
                Mail::to($email)->send(new OrderConfirmationMail($order));
            } catch (\Throwable $exception) {
                Order::query()
                    ->whereKey($order->id)
                    ->where('confirmation_sent_at', $mailClaimedAt)
                    ->update(['confirmation_sent_at' => null]);

                Log::error('Order confirmation email could not be sent.', [
                    'order_id' => $order->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        return $order;
    }

    private function findOrderForPaymentIntent(object $paymentIntent): ?Order
    {
        $paymentIntentId = $paymentIntent->id ?? null;
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if (! $paymentIntentId && ! $orderId) {
            return null;
        }

        return Order::query()
            ->when($paymentIntentId, fn ($query) => $query->where('stripe_transaction_id', $paymentIntentId))
            ->when($paymentIntentId && $orderId, fn ($query) => $query->orWhere('id', $orderId))
            ->when(! $paymentIntentId && $orderId, fn ($query) => $query->where('id', $orderId))
            ->first();
    }

    private function findOrderForCheckoutSession(object $checkoutSession): ?Order
    {
        $sessionId = $checkoutSession->id ?? null;
        $orderId = $checkoutSession->client_reference_id
            ?? $checkoutSession->metadata->order_id
            ?? null;

        if (! $sessionId && ! $orderId) {
            return null;
        }

        return Order::query()
            ->when($sessionId, fn ($query) => $query->where('stripe_checkout_session_id', $sessionId))
            ->when($sessionId && $orderId, fn ($query) => $query->orWhere('id', $orderId))
            ->when(! $sessionId && $orderId, fn ($query) => $query->where('id', $orderId))
            ->first();
    }

    private function paymentIntentCustomerDetails(object $paymentIntent): array
    {
        $shipping = $paymentIntent->shipping ?? null;
        $paymentMethod = is_object($paymentIntent->payment_method ?? null)
            ? $paymentIntent->payment_method
            : null;
        $billing = $paymentMethod->billing_details ?? null;
        $address = $shipping->address ?? $billing->address ?? null;

        return [
            'name' => $shipping->name ?? $billing->name ?? null,
            'email' => $paymentIntent->receipt_email ?? $billing->email ?? null,
            'phone' => $shipping->phone ?? $billing->phone ?? null,
            'address' => $this->addressArray($address),
        ];
    }

    private function checkoutCustomerDetails(object $checkoutSession): array
    {
        $customer = $checkoutSession->customer_details ?? null;
        $shipping = $checkoutSession->shipping_details
            ?? $checkoutSession->collected_information->shipping_details
            ?? null;
        $address = $shipping->address ?? $customer->address ?? null;

        return [
            'name' => $shipping->name ?? $customer->name ?? null,
            'email' => $customer->email ?? null,
            'phone' => $shipping->phone ?? $customer->phone ?? null,
            'address' => $this->addressArray($address),
        ];
    }

    private function addressArray(?object $address): array
    {
        return [
            'line1' => $address->line1 ?? null,
            'line2' => $address->line2 ?? null,
            'city' => $address->city ?? null,
            'state' => $address->state ?? null,
            'postal_code' => $address->postal_code ?? null,
            'country' => $address->country ?? null,
        ];
    }
}
