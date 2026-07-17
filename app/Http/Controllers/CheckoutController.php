<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CheckoutController extends Controller
{
    /**
     * Creează un PaymentIntent și returnează view-ul cu checkout-ul integrat.
     */
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Colecția dumneavoastră este goală.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        try {
            // Creeaza un pending order pentru a putea asocia items
            $order = Order::create([
                'order_number' => 'IVORY-' . strtoupper(uniqid()),
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'shipping_status' => 'processing',
                'customer_details' => [], // Vom updata prin Webhook sau Element
            ]);

            foreach ($cart as $id => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            // Folosim un PaymentIntent în loc de Checkout Session
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $totalAmount * 100, // În bani
                'currency' => 'ron',
                // Putem stoca un identificator pentru a lega intent-ul de coș
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);

            $order->update(['stripe_transaction_id' => $paymentIntent->id]);

            return view('checkout.index', [
                'clientSecret' => $paymentIntent->client_secret,
                'totalAmount' => $totalAmount,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'A apărut o problemă la inițializarea plății: ' . $e->getMessage());
        }
    }

    /**
     * După plata reușită. Logica de creare a comenzii este acum în Webhook.
     */
    public function success(Request $request)
    {
        $paymentIntent = $request->get('payment_intent');

        // Preluăm comanda după payment intent dacă s-a procesat deja de webhook
        $order = null;
        if ($paymentIntent) {
            $order = Order::where('stripe_transaction_id', $paymentIntent)->first();
        }

        // Golim coșul
        session()->forget('cart');

        return view('checkout.success', compact('order'));
    }

    /**
     * Dacă plata este anulată (din pagina Stripe).
     */
    public function cancel()
    {
        return redirect()->route('cart.index')->with('error', 'Plata a fost anulată. Puteți relua procesul oricând.');
    }
}
