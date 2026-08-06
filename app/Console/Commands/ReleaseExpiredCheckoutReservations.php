<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderPaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class ReleaseExpiredCheckoutReservations extends Command
{
    protected $signature = 'checkout:release-expired-reservations';

    protected $description = 'Cancel abandoned Stripe PaymentIntents and return their reserved stock';

    public function handle(OrderPaymentService $payments): int
    {
        $cutoff = now()->subMinutes(max(5, (int) config('shop.checkout_reservation_minutes', 31)));
        $stripe = new StripeClient((string) config('services.stripe.secret'));
        $released = 0;

        Order::query()
            ->where('payment_status', 'pending')
            ->whereNotNull('stock_reserved_at')
            ->whereNull('stock_released_at')
            ->where('stock_reserved_at', '<=', $cutoff)
            ->chunkById(100, function ($orders) use ($stripe, $payments, &$released): void {
                foreach ($orders as $order) {
                    try {
                        if (! $order->stripe_transaction_id) {
                            $order->update(['payment_status' => 'failed']);
                            $order->releaseReservedStock();
                            $released++;

                            continue;
                        }

                        $paymentIntent = $stripe->paymentIntents->retrieve($order->stripe_transaction_id);

                        if ($paymentIntent->status === 'succeeded') {
                            $payments->completePaymentIntent($paymentIntent);

                            continue;
                        }

                        if ($paymentIntent->status === 'processing') {
                            continue;
                        }

                        if ($paymentIntent->status !== 'canceled') {
                            $paymentIntent = $stripe->paymentIntents->cancel($paymentIntent->id);
                        }

                        $payments->cancelPayment($paymentIntent);
                        $released++;
                    } catch (\Throwable $exception) {
                        Log::warning('Expired checkout reservation could not be released.', [
                            'order_id' => $order->id,
                            'exception' => $exception->getMessage(),
                        ]);
                    }
                }
            });

        $this->info("Released {$released} expired checkout reservation(s).");

        return self::SUCCESS;
    }
}
