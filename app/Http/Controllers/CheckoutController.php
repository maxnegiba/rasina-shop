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
     * Crează o sesiune Stripe și redirecționează utilizatorul.
     */
    public function createSession(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Colecția dumneavoastră este goală.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $lineItems = [];

        foreach ($cart as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'ron',
                    'product_data' => [
                        'name' => $item['name'],
                        // Puteți adăuga imagini și aici, dar trebuie să fie URL-uri externe valide
                        // 'images' => [$item['image']],
                    ],
                    'unit_amount' => $item['price'] * 100, // Stripe așteaptă suma în bani (cenți/bani)
                ],
                'quantity' => $item['quantity'],
            ];
        }

        try {
            $checkoutSession = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel'),
                'shipping_address_collection' => [
                    'allowed_countries' => ['RO'], // Permitem doar România momentan, dar se poate ajusta
                ],
            ]);

            return redirect($checkoutSession->url);

        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'A apărut o problemă la inițializarea plății: ' . $e->getMessage());
        }
    }

    /**
     * După plata reușită: curățăm coșul și creăm comanda.
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('home');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return redirect()->route('cart.index')->with('error', 'Plata nu a fost finalizată.');
            }

            // Aici ar trebui să verificăm dacă am procesat deja această comandă
            // Pentru simplitate (fiindcă nu folosim webhook-uri), o vom crea acum dacă nu există.

            $existingOrder = Order::where('stripe_transaction_id', $sessionId)->first();

            if (!$existingOrder) {
                // Extragem detaliile clientului din Stripe
                $customerDetails = [
                    'name' => $session->customer_details->name,
                    'email' => $session->customer_details->email,
                    'phone' => $session->customer_details->phone,
                    'address' => [
                        'line1' => $session->customer_details->address->line1,
                        'line2' => $session->customer_details->address->line2,
                        'city' => $session->customer_details->address->city,
                        'state' => $session->customer_details->address->state,
                        'postal_code' => $session->customer_details->address->postal_code,
                        'country' => $session->customer_details->address->country,
                    ]
                ];

                $order = Order::create([
                    'order_number' => 'IVORY-' . strtoupper(uniqid()),
                    'total_amount' => $session->amount_total / 100,
                    'payment_status' => 'paid',
                    'shipping_status' => 'processing',
                    'customer_details' => $customerDetails,
                    'stripe_transaction_id' => $sessionId,
                ]);

                // Adăugăm produsele din coș în baza de date
                $cart = session()->get('cart', []);
                foreach ($cart as $id => $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);

                    // Tot aici ar trebui să scădem stocul produselor
                    $product = \App\Models\Product::find($id);
                    if ($product) {
                        $product->decrement('stock', $item['quantity']);
                    }
                }
            }

            // Golim coșul
            session()->forget('cart');

            return view('checkout.success');

        } catch (\Exception $e) {
            // Dacă ceva dă eroare la preluarea sesiunii din Stripe
            return redirect()->route('home')->with('error', 'O eroare a apărut: ' . $e->getMessage());
        }
    }

    /**
     * Dacă plata este anulată (din pagina Stripe).
     */
    public function cancel()
    {
        return redirect()->route('cart.index')->with('error', 'Plata a fost anulată. Puteți relua procesul oricând.');
    }
}
