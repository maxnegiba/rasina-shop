<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CheckoutOptionsController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Colecția dumneavoastră este goală.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('checkout.index', compact('cart', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required_if:shipping_method,home|string|max:255|nullable',
            'city' => 'required_if:shipping_method,home|string|max:100|nullable',
            'county' => 'required_if:shipping_method,home|string|max:100|nullable',
            'shipping_method' => 'required|in:home,locker',
            'easybox_id' => 'required_if:shipping_method,locker|string|nullable',
            'easybox_name' => 'required_if:shipping_method,locker|string|nullable',
            'easybox_address' => 'required_if:shipping_method,locker|string|nullable',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Colecția dumneavoastră este goală.');
        }

        Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));

        $lineItems = [];
        foreach ($cart as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'ron',
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => $item['price'] * 100,
                ],
                'quantity' => $item['quantity'],
            ];
        }

        // Determinăm costul transportului
        $shippingCost = $request->shipping_method === 'locker' ? 15 : 20; // 15 RON Easybox, 20 RON Curier

        $shippingOptions = [
            [
                'shipping_rate_data' => [
                    'type' => 'fixed_amount',
                    'fixed_amount' => [
                        'amount' => $shippingCost * 100,
                        'currency' => 'ron',
                    ],
                    'display_name' => $request->shipping_method === 'locker' ? 'Livrare la Easybox' : 'Livrare la Domiciliu',
                ],
            ]
        ];

        // Salvăm detaliile în metadata pentru webhook
        $metadata = [
            'customer_name' => $request->name,
            'customer_email' => $request->email,
            'customer_phone' => $request->phone,
            'shipping_method' => $request->shipping_method,
            'shipping_cost' => $shippingCost,
        ];

        if ($request->shipping_method === 'home') {
            $metadata['address'] = $request->address;
            $metadata['city'] = $request->city;
            $metadata['county'] = $request->county;
        } else {
            $metadata['easybox_id'] = $request->easybox_id;
            $metadata['easybox_name'] = $request->easybox_name;
            $metadata['easybox_address'] = $request->easybox_address;
        }

        try {
            $checkoutSession = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $request->email,
                'shipping_options' => $shippingOptions,
                'metadata' => $metadata,
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel'),
            ]);

            return redirect($checkoutSession->url);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'A apărut o problemă la inițializarea plății: ' . $e->getMessage());
        }
    }
}
