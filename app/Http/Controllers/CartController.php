<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Afișează conținutul coșului de cumpărături.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $checkoutToken = (string) $request->session()->get('checkout_order_token');

        if ($checkoutToken !== '' && Order::query()
            ->where('public_token', $checkoutToken)
            ->where('payment_status', 'pending')
            ->whereNull('stock_released_at')
            ->exists()) {
            return redirect()->route('checkout.index')
                ->with('error', 'Există o plată în curs. Folosiți opțiunea „Modifică” pentru a reveni în siguranță la coș.');
        }

        $cart = $this->refreshCart($request->session()->get('cart', []));
        $request->session()->put('cart', $cart);
        $summary = $this->summary($cart);

        return view('cart.index', compact('cart', 'summary'));
    }

    /**
     * Adaugă un produs în coșul de cumpărături.
     */
    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'redirect_to_checkout' => ['nullable', 'boolean'],
            'request_token' => ['nullable', 'uuid'],
        ], [
            'product_id.required' => 'Selectați un produs.',
            'product_id.integer' => 'Produsul selectat nu este valid.',
            'product_id.exists' => 'Produsul selectat nu mai există.',
            'quantity.integer' => 'Cantitatea trebuie să fie un număr întreg.',
            'quantity.min' => 'Cantitatea minimă este 1.',
            'redirect_to_checkout.boolean' => 'Acțiunea selectată nu este validă.',
            'request_token.uuid' => 'Cererea de adăugare nu este validă. Reîncărcați pagina.',
        ]);

        $requestToken = (string) ($validated['request_token'] ?? '');
        $cart = session()->get('cart', []);

        if ($requestToken !== '' && in_array($requestToken, session()->get('cart_add_tokens', []), true)) {
            if ($this->expectsJson($request)) {
                return response()->json($this->cartPayload(
                    $cart,
                    'Produsul este deja în coș.',
                    $request->boolean('redirect_to_checkout') ? route('checkout.index') : null,
                ));
            }

            return $request->boolean('redirect_to_checkout')
                ? redirect()->route('checkout.index')
                : redirect()->back()->with('success', 'Produsul este deja în coș.');
        }

        $product = Product::with('images')->findOrFail($validated['product_id']);

        if ($product->status !== 'published' || ! $product->isPurchasable()) {
            $message = $product->price === null || (float) $product->price <= 0
                ? 'Produsul nu poate fi cumpărat până când nu are un preț valid.'
                : 'Produsul nu mai este în stoc.';

            return $this->errorResponse($request, $message);
        }

        $quantity = (int) ($validated['quantity'] ?? 1);

        if (isset($cart[$product->id])) {
            $newQuantity = (int) $cart[$product->id]['quantity'] + $quantity;

            if ($newQuantity > $product->stock) {
                return $this->errorResponse(
                    $request,
                    'Nu poți adăuga mai multe bucăți decât stocul disponibil.'
                );
            }

            $cart[$product->id]['quantity'] = $newQuantity;
        } else {
            if ($quantity > $product->stock) {
                return $this->errorResponse(
                    $request,
                    'Cantitatea solicitată depășește stocul disponibil.'
                );
            }

            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'quantity' => $quantity,
                'price' => (float) $product->price,
                'image' => $this->productImageUrl($product),
                'stock' => (int) $product->stock,
            ];
        }

        session()->put('cart', $cart);
        $this->rememberAddToken($requestToken);

        if ($this->expectsJson($request)) {
            return response()->json($this->cartPayload(
                $cart,
                'Produsul a fost adăugat în coș.',
                $request->boolean('redirect_to_checkout') ? route('checkout.index') : null,
            ));
        }

        if ($request->boolean('redirect_to_checkout')) {
            return redirect()->route('checkout.index');
        }

        return redirect()->back()->with('success', 'Produsul a fost adăugat în coș.');
    }

    /**
     * Actualizează cantitatea unui produs, fără a permite depășirea stocului.
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:0'],
        ], [
            'id.required' => 'Produsul nu a fost identificat.',
            'id.integer' => 'Produsul selectat nu este valid.',
            'id.exists' => 'Produsul selectat nu mai există.',
            'quantity.required' => 'Introduceți cantitatea dorită.',
            'quantity.integer' => 'Cantitatea trebuie să fie un număr întreg.',
            'quantity.min' => 'Cantitatea nu poate fi negativă.',
        ]);

        $cart = $request->session()->get('cart', []);
        $productId = (int) $validated['id'];
        $quantity = (int) $validated['quantity'];

        if (! isset($cart[$productId])) {
            return $this->errorResponse($request, 'Produsul nu mai există în coș. Reîncărcați pagina.');
        }

        if ($quantity === 0) {
            unset($cart[$productId]);
            $message = 'Produsul a fost eliminat din coș.';
        } else {
            $product = Product::with('images')->findOrFail($productId);

            if ($product->status !== 'published' || ! $product->isPurchasable()) {
                return $this->errorResponse($request, 'Produsul nu mai este disponibil pentru cumpărare.');
            }

            if ($quantity > (int) $product->stock) {
                return $this->errorResponse(
                    $request,
                    'Sunt disponibile cel mult '.$product->stock.' bucăți din acest produs.'
                );
            }

            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'quantity' => $quantity,
                'price' => (float) $product->price,
                'image' => $this->productImageUrl($product),
                'stock' => (int) $product->stock,
            ];
            $message = 'Cantitatea a fost actualizată.';
        }

        $request->session()->put('cart', $cart);

        if ($this->expectsJson($request)) {
            return response()->json($this->cartPayload($cart, $message));
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    /**
     * Elimină un produs din coșul de cumpărături.
     */
    public function remove(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ], [
            'id.required' => 'Produsul nu a fost identificat.',
            'id.integer' => 'Produsul selectat nu este valid.',
        ]);

        $cart = session()->get('cart', []);
        unset($cart[$validated['id']]);
        session()->put('cart', $cart);

        if ($this->expectsJson($request)) {
            return response()->json($this->cartPayload(
                $cart,
                'Produsul a fost eliminat din coș.'
            ));
        }

        return redirect()->back()->with('success', 'Produsul a fost eliminat din coș.');
    }

    private function expectsJson(Request $request): bool
    {
        return $request->ajax() || $request->expectsJson() || $request->wantsJson();
    }

    private function cartPayload(array $cart, string $message, ?string $redirectUrl = null): array
    {
        $summary = $this->summary($cart);

        return [
            'success' => true,
            'message' => $message,
            'cart_count' => $summary['item_count'],
            'summary' => $summary,
            'redirect_url' => $redirectUrl,
            'html' => view('cart._sidebar_content', compact('cart', 'summary'))->render(),
        ];
    }

    private function refreshCart(array $cart): array
    {
        if ($cart === []) {
            return [];
        }

        $products = Product::query()
            ->with('images')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->mapWithKeys(function (array $item, int|string $productId) use ($products): array {
                /** @var Product|null $product */
                $product = $products->get((int) $productId);

                if (! $product || $product->status !== 'published' || ! $product->isPurchasable()) {
                    return [];
                }

                $quantity = min(max(1, (int) ($item['quantity'] ?? 1)), (int) $product->stock);

                return [$product->id => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'quantity' => $quantity,
                    'price' => (float) $product->price,
                    'image' => $this->productImageUrl($product),
                    'stock' => (int) $product->stock,
                ]];
            })
            ->all();
    }

    private function summary(array $cart): array
    {
        $subtotal = round((float) collect($cart)->sum(
            fn (array $item): float => (float) $item['price'] * (int) $item['quantity']
        ), 2);
        $shipping = $cart === [] ? 0.0 : round((float) config('shop.shipping_cost', 0), 2);
        $discount = 0.0;

        return [
            'item_count' => (int) collect($cart)->sum(fn (array $item): int => (int) $item['quantity']),
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => round($subtotal + $shipping - $discount, 2),
        ];
    }

    private function productImageUrl(Product $product): string
    {
        $featuredImage = $product->images->firstWhere('is_featured', true)
            ?? $product->images->first();

        return ! empty($product->image)
            ? asset('storage/'.$product->image)
            : ($featuredImage
                ? asset('storage/'.$featuredImage->image_path)
                : asset('img/logo.png'));
    }

    private function rememberAddToken(string $requestToken): void
    {
        if ($requestToken === '') {
            return;
        }

        $tokens = session()->get('cart_add_tokens', []);
        $tokens[] = $requestToken;
        session()->put('cart_add_tokens', array_slice(array_values(array_unique($tokens)), -50));
    }

    private function errorResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($this->expectsJson($request)) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        return redirect()->back()->with('error', $message);
    }
}
