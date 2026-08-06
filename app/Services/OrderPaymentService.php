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
    public function completeCheckout(object $checkoutSession): ?Order
    {
        if (($checkoutSession->payment_status ?? null) !== 'paid') {
            return null;
        }

        $order = $this->findOrderForSession($checkoutSession);

        if (! $order) {
            Log::error('Order not found for Stripe Checkout Session.', [
                'checkout_session_id' => $checkoutSession->id ?? null,
            ]);

            return null;
        }

        $expectedAmount = (int) round((float) $order->total_amount * 100);
        $paidAmount = (int) ($checkoutSession->amount_total ?? -1);
        $currency = strtolower((string) ($checkoutSession->currency ?? ''));

        if ($paidAmount !== $expectedAmount || $currency !== 'ron') {
            Log::critical('Stripe Checkout amount does not match the order.', [
                'order_id' => $order->id,
                'expected_amount' => $expectedAmount,
                'paid_amount' => $paidAmount,
                'currency' => $currency,
            ]);

            throw new RuntimeException('Stripe payment amount mismatch.');
        }

        $customerDetails = $this->customerDetails($checkoutSession);
        $email = filter_var($customerDetails['email'] ?? null, FILTER_VALIDATE_EMAIL) ?: null;
        $mailClaimedAt = null;

        DB::transaction(function () use ($order, $checkoutSession, $customerDetails, $email, &$mailClaimedAt): void {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            $updates = [
                'payment_status' => 'paid',
                'customer_details' => $customerDetails,
                'stripe_checkout_session_id' => $checkoutSession->id,
                'stripe_transaction_id' => $this->paymentIntentId($checkoutSession),
            ];

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

    public function failPayment(object $paymentIntent): void
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;
        $order = $orderId
            ? Order::find($orderId)
            : Order::where('stripe_transaction_id', $paymentIntent->id ?? null)->first();

        if (! $order || $order->payment_status === 'paid') {
            return;
        }

        $order->update([
            'payment_status' => 'failed',
            'stripe_transaction_id' => $paymentIntent->id ?? $order->stripe_transaction_id,
        ]);
        $order->releaseReservedStock();
    }

    public function expireCheckout(object $checkoutSession): void
    {
        $order = $this->findOrderForSession($checkoutSession);

        if (! $order || $order->payment_status === 'paid') {
            return;
        }

        $order->update(['payment_status' => 'failed']);
        $order->releaseReservedStock();
    }

    private function findOrderForSession(object $checkoutSession): ?Order
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

    private function customerDetails(object $checkoutSession): array
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
            'address' => [
                'line1' => $address->line1 ?? null,
                'line2' => $address->line2 ?? null,
                'city' => $address->city ?? null,
                'state' => $address->state ?? null,
                'postal_code' => $address->postal_code ?? null,
                'country' => $address->country ?? null,
            ],
        ];
    }

    private function paymentIntentId(object $checkoutSession): ?string
    {
        $paymentIntent = $checkoutSession->payment_intent ?? null;

        return is_object($paymentIntent) ? ($paymentIntent->id ?? null) : $paymentIntent;
    }
}
