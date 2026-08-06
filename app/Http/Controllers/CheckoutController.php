<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Colecția dumneavoastră este goală.');
        }

        $totalAmount = $this->currentCartTotal($cart);

        if ($totalAmount === null) {
            return redirect()->route('cart.index')
                ->with('error', 'Un produs din coș nu mai este disponibil. Actualizați coșul și încercați din nou.');
        }

        return view('checkout.index', compact('cart', 'totalAmount'));
    }

    public function start(Request $request): RedirectResponse
    {
        $request->validate([
            'accept_terms' => ['accepted'],
            'acknowledge_privacy' => ['accepted'],
        ], [
            'accept_terms.accepted' => 'Trebuie să acceptați Termenii și Condițiile.',
            'acknowledge_privacy.accepted' => 'Trebuie să confirmați că ați citit Politica de Confidențialitate.',
        ]);

        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Colecția dumneavoastră este goală.');
        }

        $order = null;

        try {
            $order = DB::transaction(function () use ($cart): Order {
                $products = Product::query()
                    ->whereIn('id', array_keys($cart))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($products->count() !== count($cart)) {
                    throw new \RuntimeException('Un produs din coș nu mai există.');
                }

                $totalAmount = 0.0;
                $validatedItems = [];

                foreach ($cart as $productId => $cartItem) {
                    /** @var Product $product */
                    $product = $products->get((int) $productId);
                    $quantity = (int) ($cartItem['quantity'] ?? 0);

                    if (! $product || $product->status !== 'published' || ! $product->isPurchasable() || $quantity < 1 || $quantity > $product->stock) {
                        throw new \RuntimeException('Produsul „'.($product?->name ?? '#'.$productId).'” nu mai este disponibil în cantitatea cerută.');
                    }

                    $unitPrice = (float) $product->price;
                    $totalAmount += $unitPrice * $quantity;
                    $validatedItems[] = compact('product', 'quantity', 'unitPrice');
                }

                $order = Order::create([
                    'order_number' => 'MTD-'.now()->format('Ymd').'-'.strtoupper(bin2hex(random_bytes(4))),
                    'total_amount' => round($totalAmount, 2),
                    'payment_status' => 'pending',
                    'shipping_status' => 'processing',
                    'customer_details' => [],
                    'stock_reserved_at' => now(),
                    'terms_accepted_at' => now(),
                    'terms_version' => config('shop.terms_version'),
                ]);

                foreach ($validatedItems as $item) {
                    $order->items()->create([
                        'product_id' => $item['product']->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unitPrice'],
                    ]);

                    $item['product']->decrement('stock', $item['quantity']);
                }

                return $order->load('items.product');
            });

            $stripe = new StripeClient((string) config('services.stripe.secret'));

            $checkoutSession = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'locale' => 'ro',
                'payment_method_types' => ['card'],
                'billing_address_collection' => 'required',
                'phone_number_collection' => ['enabled' => true],
                'shipping_address_collection' => [
                    'allowed_countries' => ['RO'],
                ],
                'line_items' => $order->items->map(fn ($item): array => [
                    'quantity' => $item->quantity,
                    'price_data' => [
                        'currency' => 'ron',
                        'unit_amount' => (int) round((float) $item->unit_price * 100),
                        'product_data' => [
                            'name' => (string) ($item->product?->name ?? 'Produs MTD Art'),
                        ],
                    ],
                ])->values()->all(),
                'client_reference_id' => (string) $order->id,
                'metadata' => ['order_id' => (string) $order->id],
                'payment_intent_data' => [
                    'metadata' => ['order_id' => (string) $order->id],
                ],
                'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => URL::temporarySignedRoute(
                    'checkout.cancel',
                    now()->addHour(),
                    ['order' => $order->public_token],
                ),
                'expires_at' => now()->addMinutes(31)->timestamp,
            ], [
                'idempotency_key' => 'mtd-order-'.$order->id,
            ]);

            $order->update(['stripe_checkout_session_id' => $checkoutSession->id]);

            return redirect()->away($checkoutSession->url, 303);
        } catch (\Throwable $exception) {
            if ($order) {
                $order->update(['payment_status' => 'failed']);
                $order->releaseReservedStock();
            }

            Log::error('Stripe Checkout could not be initialized.', [
                'order_id' => $order?->id,
                'exception' => $exception->getMessage(),
            ]);

            return redirect()->route('cart.index')
                ->with('error', 'Plata nu a putut fi inițializată. Produsele au rămas în coș; încercați din nou.');
        }
    }

    public function success(Request $request, OrderPaymentService $payments): View
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string', 'max:255'],
        ]);

        $order = Order::where('stripe_checkout_session_id', $validated['session_id'])->first();

        try {
            $stripe = new StripeClient((string) config('services.stripe.secret'));
            $checkoutSession = $stripe->checkout->sessions->retrieve($validated['session_id']);
            $order = $payments->completeCheckout($checkoutSession) ?? $order;
        } catch (\Throwable $exception) {
            Log::warning('Stripe Checkout success page could not refresh the payment state.', [
                'checkout_session_id' => $validated['session_id'],
                'exception' => $exception->getMessage(),
            ]);
        }

        if ($order?->payment_status === 'paid') {
            $request->session()->forget('cart');
        }

        return view('checkout.success', compact('order'));
    }

    public function cancel(Order $order, OrderPaymentService $payments): RedirectResponse
    {
        if ($order->payment_status !== 'paid') {
            if ($order->stripe_checkout_session_id) {
                try {
                    $stripe = new StripeClient((string) config('services.stripe.secret'));
                    $expiredSession = $stripe->checkout->sessions->expire($order->stripe_checkout_session_id);
                    $payments->expireCheckout($expiredSession);
                } catch (\Throwable $exception) {
                    Log::notice('Stripe Checkout Session could not be expired on cancellation.', [
                        'order_id' => $order->id,
                        'exception' => $exception->getMessage(),
                    ]);
                }
            } else {
                $order->update(['payment_status' => 'failed']);
                $order->releaseReservedStock();
            }
        }

        return redirect()->route('cart.index')
            ->with('error', 'Plata a fost anulată. Produsele au rămas în coș.');
    }

    private function currentCartTotal(array $cart): ?float
    {
        $products = Product::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        if ($products->count() !== count($cart)) {
            return null;
        }

        $total = 0.0;

        foreach ($cart as $productId => $item) {
            $product = $products->get((int) $productId);
            $quantity = (int) ($item['quantity'] ?? 0);

            if (! $product || $product->status !== 'published' || ! $product->isPurchasable() || $quantity < 1 || $quantity > $product->stock) {
                return null;
            }

            $total += (float) $product->price * $quantity;
        }

        return round($total, 2);
    }
}
