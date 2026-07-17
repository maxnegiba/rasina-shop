<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Barryvdh\DomPDF\Facade\Pdf;

class WebhookController extends Controller
{
    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->processOrder($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;

                $order = Order::where('stripe_transaction_id', $paymentIntent->id)->first();
                if ($order) {
                    $order->update(['payment_status' => 'failed']);
                }
                break;

            default:
                Log::info('Received unhandled Stripe event: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    private function processOrder($paymentIntent)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $order = Order::where('stripe_transaction_id', $paymentIntent->id)->first();

        if (!$order) {
            Log::error('Order not found for PaymentIntent: ' . $paymentIntent->id);
            return;
        }

        if ($order->payment_status === 'paid') {
            return; // Already processed
        }

        // 1. Extragem datele clientului din charge
        $chargeId = null;
        if (isset($paymentIntent->latest_charge)) {
            $chargeId = is_string($paymentIntent->latest_charge) ? $paymentIntent->latest_charge : $paymentIntent->latest_charge->id;
        }

        $charge = $chargeId ? \Stripe\Charge::retrieve($chargeId) : null;

        $customerDetails = [
            'name' => $charge->billing_details->name ?? 'Nume Necunoscut',
            'email' => $charge->billing_details->email ?? 'email@lipsa.ro',
            'phone' => $charge->billing_details->phone ?? null,
            'address' => [
                'line1' => $charge->billing_details->address->line1 ?? null,
                'line2' => $charge->billing_details->address->line2 ?? null,
                'city' => $charge->billing_details->address->city ?? null,
                'state' => $charge->billing_details->address->state ?? null,
                'postal_code' => $charge->billing_details->address->postal_code ?? null,
                'country' => $charge->billing_details->address->country ?? null,
            ]
        ];

        // 2. Update order status and assign proforma
        $order->update([
            'payment_status' => 'paid',
            'proforma_number' => Order::generateProformaNumber(),
            'customer_details' => $customerDetails,
        ]);

        // 3. Update stock for items
        $order->load('items.product');
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->decrement('stock', $item->quantity);
            }
        }

        // 4. Generare PDF Proforma
        $pdf = Pdf::loadView('pdf.proforma', compact('order'));
        $pdfPath = storage_path('app/temp_proforma_' . $order->id . '.pdf');
        $pdf->save($pdfPath);

        // 5. Trimitem email
        if (!empty($customerDetails['email']) && $customerDetails['email'] !== 'email@lipsa.ro') {
            Mail::to($customerDetails['email'])->send(new OrderConfirmationMail($order, $pdfPath));
        }

        // Curatare fisier temporar
        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }
    }
}
