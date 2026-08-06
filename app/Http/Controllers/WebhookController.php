<?php

namespace App\Http\Controllers;

use App\Services\OrderPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        } catch (\Throwable $exception) {
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
