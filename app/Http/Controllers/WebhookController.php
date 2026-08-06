<?php

namespace App\Http\Controllers;

use App\Services\OrderPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handleStripeWebhook(Request $request, OrderPaymentService $payments): JsonResponse
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                (string) config('services.stripe.webhook_secret'),
            );
        } catch (\UnexpectedValueException) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $eventId = (string) ($event->id ?? '');

        if ($eventId === '') {
            return response()->json(['error' => 'Missing event id'], 400);
        }

        $inserted = DB::table('stripe_webhook_events')->insertOrIgnore([
            'id' => $eventId,
            'type' => $event->type,
            'status' => 'processing',
            'attempts' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $shouldProcess = $inserted === 1 || DB::transaction(function () use ($eventId): bool {
            $record = DB::table('stripe_webhook_events')
                ->where('id', $eventId)
                ->lockForUpdate()
                ->first();

            if (! $record || $record->status === 'completed') {
                return false;
            }

            if ($record->status === 'processing'
                && $record->updated_at
                && now()->diffInMinutes(Carbon::parse($record->updated_at)) < 5) {
                return false;
            }

            DB::table('stripe_webhook_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'processing',
                    'attempts' => DB::raw('attempts + 1'),
                    'last_error' => null,
                    'updated_at' => now(),
                ]);

            return true;
        });

        if (! $shouldProcess) {
            return response()->json(['status' => 'already_processed']);
        }

        try {
            match ($event->type) {
                'payment_intent.succeeded' => $payments->completePaymentIntent($event->data->object),
                'payment_intent.payment_failed' => $payments->recordFailedAttempt($event->data->object),
                'payment_intent.canceled' => $payments->cancelPayment($event->data->object),
                'checkout.session.completed',
                'checkout.session.async_payment_succeeded' => $payments->completeLegacyCheckout($event->data->object),
                'checkout.session.expired',
                'checkout.session.async_payment_failed' => $payments->expireLegacyCheckout($event->data->object),
                default => Log::debug('Ignored Stripe event.', ['type' => $event->type]),
            };

            DB::table('stripe_webhook_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                    'last_error' => null,
                    'updated_at' => now(),
                ]);
        } catch (\Throwable $exception) {
            DB::table('stripe_webhook_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'failed',
                    'last_error' => mb_substr($exception->getMessage(), 0, 5000),
                    'updated_at' => now(),
                ]);

            Log::error('Stripe webhook processing failed.', [
                'event_id' => $event->id ?? null,
                'event_type' => $event->type,
                'exception' => $exception->getMessage(),
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }

        return response()->json(['status' => 'success']);
    }
}
