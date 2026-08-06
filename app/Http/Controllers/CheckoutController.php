<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function index(Request $request, OrderPaymentService $payments): View|RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Colecția dumneavoastră este goală.');
        }

        $order = $this->reusableOrder($request, $cart);

        try {
            $stripe = new StripeClient((string) config('services.stripe.secret'));

            if ($order?->stripe_transaction_id) {
                $paymentIntent = $stripe->paymentIntents->retrieve($order->stripe_transaction_id);

                if ($paymentIntent->status === 'succeeded') {
                    $payments->completePaymentIntent($paymentIntent);

                    return redirect()->route('checkout.success', [
                        'payment_intent' => $paymentIntent->id,
                    ]);
                }

                if ($paymentIntent->status === 'canceled') {
                    $order->update(['payment_status' => 'failed']);
                    $order->releaseReservedStock();
                    $order = null;
                }
            }

            if (! $order) {
                $completedOrder = $this->cancelStaleSessionOrder($request, $stripe, $payments);

                if ($completedOrder) {
                    return redirect()->route('checkout.success', [
                        'payment_intent' => $completedOrder->stripe_transaction_id,
                    ]);
                }

                $order = $this->reserveCart($cart);

                $paymentIntent = $stripe->paymentIntents->create([
                    'amount' => (int) round((float) $order->total_amount * 100),
                    'currency' => 'ron',
                    'automatic_payment_methods' => ['enabled' => true],
                    'metadata' => [
                        'order_id' => (string) $order->id,
                        'order_number' => $order->order_number,
                    ],
                ], [
                    'idempotency_key' => 'mtd-payment-intent-'.$order->id,
                ]);

                $order->update(['stripe_transaction_id' => $paymentIntent->id]);
                $request->session()->put('checkout_order_token', $order->public_token);
            }

            return view('checkout.index', [
                'clientSecret' => $paymentIntent->client_secret,
                'stripeKey' => (string) config('services.stripe.key'),
                'orderToken' => $order->public_token,
                'totalAmount' => (float) $order->total_amount,
            ]);
        } catch (\Throwable $exception) {
            if ($order && ! $order->stripe_transaction_id) {
                $order->update(['payment_status' => 'failed']);
                $order->releaseReservedStock();
            }

            Log::error('Stripe Payment Element could not be initialized.', [
                'order_id' => $order?->id,
                'exception' => $exception->getMessage(),
            ]);

            return redirect()->route('cart.index')
                ->with('error', 'Plata nu a putut fi inițializată. Produsele au rămas în coș; încercați din nou.');
        }
    }

    public function acceptTerms(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_token' => ['required', 'uuid'],
            'accept_terms' => ['accepted'],
            'acknowledge_privacy' => ['accepted'],
        ], [
            'accept_terms.accepted' => 'Trebuie să acceptați Termenii și Condițiile.',
            'acknowledge_privacy.accepted' => 'Trebuie să confirmați că ați citit Politica de Confidențialitate.',
        ]);

        if (! hash_equals(
            (string) $request->session()->get('checkout_order_token'),
            (string) $validated['order_token'],
        )) {
            return response()->json(['message' => 'Sesiunea de plată nu mai este validă. Reîncărcați pagina.'], 403);
        }

        $updated = Order::query()
            ->where('public_token', $validated['order_token'])
            ->where('payment_status', 'pending')
            ->whereNull('stock_released_at')
            ->update([
                'terms_accepted_at' => now(),
                'privacy_acknowledged_at' => now(),
                'terms_version' => config('shop.terms_version'),
            ]);

        if ($updated !== 1) {
            return response()->json(['message' => 'Comanda nu mai poate fi plătită. Reîncărcați pagina.'], 409);
        }

        return response()->json(['accepted' => true]);
    }

    public function success(Request $request, OrderPaymentService $payments): View
    {
        $paymentIntentId = $request->string('payment_intent')->toString();
        $order = $paymentIntentId
            ? Order::where('stripe_transaction_id', $paymentIntentId)->first()
            : null;

        if ($paymentIntentId) {
            try {
                $stripe = new StripeClient((string) config('services.stripe.secret'));
                $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

                if ($paymentIntent->status === 'succeeded') {
                    $order = $payments->completePaymentIntent($paymentIntent) ?? $order;
                }
            } catch (\Throwable $exception) {
                Log::warning('Stripe payment state could not be refreshed on the success page.', [
                    'payment_intent_id' => $paymentIntentId,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        if ($order?->payment_status === 'paid') {
            $request->session()->forget(['cart', 'checkout_order_token']);
        }

        return view('checkout.success', compact('order'));
    }

    public function cancel(Request $request): RedirectResponse
    {
        $token = (string) $request->session()->get('checkout_order_token');
        $order = $token
            ? Order::where('public_token', $token)->where('payment_status', 'pending')->first()
            : null;

        if ($order) {
            try {
                if ($order->stripe_transaction_id) {
                    $stripe = new StripeClient((string) config('services.stripe.secret'));
                    $stripe->paymentIntents->cancel($order->stripe_transaction_id);
                }

                $order->update(['payment_status' => 'failed']);
                $order->releaseReservedStock();
                $request->session()->forget('checkout_order_token');
            } catch (\Throwable $exception) {
                Log::warning('Pending PaymentIntent could not be canceled.', [
                    'order_id' => $order->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        } else {
            $request->session()->forget('checkout_order_token');
        }

        return redirect()->route('cart.index')
            ->with('error', 'Plata a fost anulată. Produsele au rămas în coș.');
    }

    private function reserveCart(array $cart): Order
    {
        return DB::transaction(function () use ($cart): Order {
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
    }

    private function reusableOrder(Request $request, array $cart): ?Order
    {
        $token = (string) $request->session()->get('checkout_order_token');

        if ($token === '') {
            return null;
        }

        $order = Order::query()
            ->with('items.product')
            ->where('public_token', $token)
            ->where('payment_status', 'pending')
            ->whereNotNull('stock_reserved_at')
            ->whereNull('stock_released_at')
            ->first();

        if (! $order || count($cart) !== $order->items->count()) {
            return null;
        }

        foreach ($order->items as $item) {
            $cartItem = $cart[$item->product_id] ?? null;

            if (! $cartItem
                || (int) $cartItem['quantity'] !== (int) $item->quantity
                || ! $item->product
                || $item->product->status !== 'published'
                || (float) $item->product->price !== (float) $item->unit_price) {
                return null;
            }
        }

        return $order;
    }

    private function cancelStaleSessionOrder(
        Request $request,
        StripeClient $stripe,
        OrderPaymentService $payments,
    ): ?Order
    {
        $token = (string) $request->session()->get('checkout_order_token');

        if ($token === '') {
            return null;
        }

        $order = Order::query()
            ->where('public_token', $token)
            ->whereNull('stock_released_at')
            ->first();

        if (! $order) {
            $request->session()->forget('checkout_order_token');

            return null;
        }

        if ($order->payment_status === 'paid') {
            return $order;
        }

        if ($order->payment_status !== 'pending') {
            $request->session()->forget('checkout_order_token');

            return null;
        }

        if ($order->stripe_transaction_id) {
            $paymentIntent = $stripe->paymentIntents->retrieve($order->stripe_transaction_id);

            if ($paymentIntent->status === 'succeeded') {
                return $payments->completePaymentIntent($paymentIntent);
            }

            if ($paymentIntent->status !== 'canceled') {
                $stripe->paymentIntents->cancel($order->stripe_transaction_id);
            }
        }

        $order->update(['payment_status' => 'failed']);
        $order->releaseReservedStock();
        $request->session()->forget('checkout_order_token');

        return null;
    }
}
