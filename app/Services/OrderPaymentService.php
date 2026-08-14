<?php

namespace App\Services;

use App\Jobs\SendAdminOrderNotificationEmail;
use App\Jobs\SendOrderConfirmationEmail;
use App\Models\Order;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function placeCashOnDelivery(Order $order, array $customerDetails): Order
    {
        if (! $order->terms_accepted_at || ! $order->privacy_acknowledged_at) {
            throw new RuntimeException('Legal acceptance is required before placing a cash on delivery order.');
        }

        $customerEmail = filter_var($customerDetails['email'] ?? null, FILTER_VALIDATE_EMAIL) ?: null;

        if (! $customerEmail) {
            throw new RuntimeException('A valid customer email is required for cash on delivery.');
        }

        $adminEmail = $this->adminNotificationEmail();
        $customerMailClaimedAt = null;
        $adminMailClaimedAt = null;

        DB::transaction(function () use (
            $order,
            $customerDetails,
            $customerEmail,
            $adminEmail,
            &$customerMailClaimedAt,
            &$adminMailClaimedAt,
        ): void {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->payment_status !== 'pending'
                || $lockedOrder->stock_released_at
                || $lockedOrder->cancelled_at) {
                throw new RuntimeException('Order is no longer available for cash on delivery.');
            }

            $updates = [
                'payment_method' => 'cash_on_delivery',
                'customer_details' => $customerDetails,
                'stripe_transaction_id' => null,
                'stripe_checkout_session_id' => null,
            ];

            if (! $lockedOrder->proforma_number) {
                $updates['proforma_number'] = $lockedOrder->proformaNumber();
            }

            if (! $lockedOrder->confirmation_sent_at && ! $lockedOrder->confirmation_queued_at) {
                $customerMailClaimedAt = now();
                $updates['confirmation_queued_at'] = $customerMailClaimedAt;
                $updates['confirmation_failed_at'] = null;
            }

            if ($adminEmail && ! $lockedOrder->admin_notification_sent_at && ! $lockedOrder->admin_notification_queued_at) {
                $adminMailClaimedAt = now();
                $updates['admin_notification_queued_at'] = $adminMailClaimedAt;
                $updates['admin_notification_failed_at'] = null;
            }

            $lockedOrder->update($updates);
        });

        $order->refresh()->load('items.product');

        if ($customerMailClaimedAt) {
            try {
                SendOrderConfirmationEmail::dispatch($order->id, $customerEmail);
            } catch (\Throwable $exception) {
                Order::query()
                    ->whereKey($order->id)
                    ->where('confirmation_queued_at', $customerMailClaimedAt)
                    ->update([
                        'confirmation_queued_at' => null,
                        'confirmation_failed_at' => now(),
                    ]);

                Log::error('Cash on delivery confirmation job could not be queued.', [
                    'order_id' => $order->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        if ($adminMailClaimedAt && $adminEmail) {
            try {
                SendAdminOrderNotificationEmail::dispatch($order->id, $adminEmail);
            } catch (\Throwable $exception) {
                Order::query()
                    ->whereKey($order->id)
                    ->where('admin_notification_queued_at', $adminMailClaimedAt)
                    ->update([
                        'admin_notification_queued_at' => null,
                        'admin_notification_failed_at' => now(),
                    ]);

                Log::error('Cash on delivery admin notification job could not be queued.', [
                    'order_id' => $order->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        return $order;
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

        $customerEmail = filter_var($customerDetails['email'] ?? null, FILTER_VALIDATE_EMAIL) ?: null;
        $adminEmail = $this->adminNotificationEmail();
        $customerMailClaimedAt = null;
        $adminMailClaimedAt = null;

        if (! $order->terms_accepted_at || ! $order->privacy_acknowledged_at) {
            Log::critical('A succeeded Stripe payment has no recorded legal acceptance.', [
                'order_id' => $order->id,
                'payment_intent_id' => $transactionId,
            ]);
        }

        DB::transaction(function () use (
            $order,
            $transactionId,
            $checkoutSessionId,
            $customerDetails,
            $customerEmail,
            $adminEmail,
            &$customerMailClaimedAt,
            &$adminMailClaimedAt,
        ): void {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->stripe_transaction_id
                && ! $checkoutSessionId
                && ! hash_equals((string) $lockedOrder->stripe_transaction_id, $transactionId)) {
                throw new RuntimeException('PaymentIntent does not belong to this order.');
            }

            $updates = [
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'customer_details' => $customerDetails,
                'stripe_transaction_id' => $transactionId,
            ];

            if ($checkoutSessionId) {
                $updates['stripe_checkout_session_id'] = $checkoutSessionId;
            }

            if (! $lockedOrder->proforma_number) {
                $updates['proforma_number'] = $lockedOrder->proformaNumber();
            }

            if ($customerEmail && ! $lockedOrder->confirmation_sent_at && ! $lockedOrder->confirmation_queued_at) {
                $customerMailClaimedAt = now();
                $updates['confirmation_queued_at'] = $customerMailClaimedAt;
                $updates['confirmation_failed_at'] = null;
            }

            if ($adminEmail && ! $lockedOrder->admin_notification_sent_at && ! $lockedOrder->admin_notification_queued_at) {
                $adminMailClaimedAt = now();
                $updates['admin_notification_queued_at'] = $adminMailClaimedAt;
                $updates['admin_notification_failed_at'] = null;
            }

            $lockedOrder->update($updates);
        });

        $order->refresh()->load('items.product');

        if ($customerMailClaimedAt && $customerEmail) {
            try {
                SendOrderConfirmationEmail::dispatch($order->id, $customerEmail);
            } catch (\Throwable $exception) {
                Order::query()
                    ->whereKey($order->id)
                    ->where('confirmation_queued_at', $customerMailClaimedAt)
                    ->update([
                        'confirmation_queued_at' => null,
                        'confirmation_failed_at' => now(),
                    ]);

                Log::error('Order confirmation job could not be queued.', [
                    'order_id' => $order->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        if ($adminMailClaimedAt && $adminEmail) {
            try {
                SendAdminOrderNotificationEmail::dispatch($order->id, $adminEmail);
            } catch (\Throwable $exception) {
                Order::query()
                    ->whereKey($order->id)
                    ->where('admin_notification_queued_at', $adminMailClaimedAt)
                    ->update([
                        'admin_notification_queued_at' => null,
                        'admin_notification_failed_at' => now(),
                    ]);

                Log::error('Admin order notification job could not be queued.', [
                    'order_id' => $order->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        return $order;
    }

    private function adminNotificationEmail(): ?string
    {
        $email = app(GeneralSettings::class)->contact_email ?: config('shop.legal.email');

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    private function findOrderForPaymentIntent(object $paymentIntent): ?Order
    {
        $paymentIntentId = $paymentIntent->id ?? null;
        $orderId = $paymentIntent->metadata->order_id ?? null;
        $orderNumber = $paymentIntent->metadata->order_number ?? null;

        if (! $paymentIntentId && ! $orderId) {
            return null;
        }

        $order = $paymentIntentId
            ? Order::query()->where('stripe_transaction_id', $paymentIntentId)->first()
            : null;

        if (! $order && $orderId) {
            $order = Order::query()
                ->whereKey($orderId)
                ->whereNull('stripe_transaction_id')
                ->first();
        }

        if (! $order || $order->payment_method !== 'stripe') {
            return null;
        }

        if ($orderId && (int) $orderId !== (int) $order->id) {
            return null;
        }

        if ($orderNumber && ! hash_equals((string) $order->order_number, (string) $orderNumber)) {
            return null;
        }

        return $order;
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

        $order = $sessionId
            ? Order::query()->where('stripe_checkout_session_id', $sessionId)->first()
            : null;

        if (! $order && $orderId) {
            $order = Order::query()
                ->whereKey($orderId)
                ->whereNull('stripe_checkout_session_id')
                ->first();
        }

        if (! $order || $order->payment_method !== 'stripe') {
            return null;
        }

        if ($orderId && (int) $orderId !== (int) $order->id) {
            return null;
        }

        return $order;
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
