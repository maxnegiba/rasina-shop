<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $endpoint_secret = config('services.stripe.webhook_secret') ?? env('STRIPE_WEBHOOK_SECRET');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $event = null;

        if ($endpoint_secret) {
            try {
                $event = Webhook::constructEvent(
                    $payload, $sig_header, $endpoint_secret
                );
            } catch(\UnexpectedValueException $e) {
                // Invalid payload
                return response()->json(['error' => 'Invalid payload'], 400);
            } catch(\Stripe\Exception\SignatureVerificationException $e) {
                // Invalid signature
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        } else {
            // Dacă nu e configurat secretul (ex. local fără stripe cli), luăm event-ul direct (mai puțin sigur)
            $event = json_decode($payload);
        }

        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            $this->handleSessionCompleted($session);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleSessionCompleted($session)
    {
        // Ne asigurăm că comanda nu există deja
        $existingOrder = Order::where('stripe_transaction_id', $session->id)->first();
        if ($existingOrder) {
            return;
        }

        $metadata = $session->metadata;

        $customerDetails = [
            'name' => $metadata->customer_name ?? $session->customer_details->name,
            'email' => $metadata->customer_email ?? $session->customer_details->email,
            'phone' => $metadata->customer_phone ?? $session->customer_details->phone,
        ];

        // Dacă livrarea a fost 'home', folosim datele din metadata. Stripe poate oferi și el address, dar a noastra are judet separat.
        if (isset($metadata->shipping_method) && $metadata->shipping_method === 'home') {
            $customerDetails['address'] = [
                'line1' => $metadata->address ?? null,
                'city' => $metadata->city ?? null,
                'state' => $metadata->county ?? null,
                'country' => 'RO',
            ];
        }

        $order = Order::create([
            'order_number' => 'IVORY-' . strtoupper(uniqid()),
            'total_amount' => $session->amount_total / 100, // Stripe dă în bani
            'payment_status' => 'paid',
            'shipping_status' => 'processing',
            'customer_details' => $customerDetails,
            'stripe_transaction_id' => $session->id,
            'shipping_method' => $metadata->shipping_method ?? null,
            'shipping_cost' => $metadata->shipping_cost ?? 0,
            'easybox_id' => $metadata->easybox_id ?? null,
        ]);

        // Găsirea produselor. Din moment ce Stripe Webhook nu conține coșul nostru din sesiune,
        // trebuie să extragem produsele din Line Items Stripe-ului.

        try {
            $lineItems = \Stripe\Checkout\Session::allLineItems($session->id);
            foreach ($lineItems->data as $item) {
                // Dacă avem produsul legat (necesită preluare pe baza numelui sau id-ului, dar noi am dat doar name)
                $productName = $item->description;
                $product = Product::where('name', $productName)->first();

                if ($product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->price->unit_amount / 100,
                        'subtotal' => $item->amount_total / 100,
                    ]);

                    $product->decrement('stock', $item->quantity);
                }
            }
        } catch (\Exception $e) {
            Log::error("Eroare preluare line items pt order " . $order->id . ": " . $e->getMessage());
        }

        // După crearea comenzii, apelăm serviciul de Sameday pentru a genera AWB-ul
        try {
            $samedayService = app(\App\Services\SamedayService::class);
            $samedayService->generateAwb($order);
        } catch (\Exception $e) {
            Log::error("Eroare generare AWB pentru order " . $order->id . ": " . $e->getMessage());
        }
    }
}
