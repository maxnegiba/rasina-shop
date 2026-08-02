<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Afișează conținutul coșului de cumpărături.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn (array $item) => $item['price'] * $item['quantity']);

        return view('cart.index', compact('cart', 'total'));
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
        ]);

        $product = Product::with('images')->findOrFail($validated['product_id']);

        if (! $product->isPurchasable()) {
            $message = $product->price === null || (float) $product->price <= 0
                ? 'Produsul nu poate fi cumpărat până când nu are un preț valid.'
                : 'Produsul nu mai este în stoc.';

            return $this->errorResponse($request, $message);
        }

        $cart = session()->get('cart', []);
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

            $featuredImage = $product->images->firstWhere('is_featured', true)
                ?? $product->images->first();

            $imageUrl = ! empty($product->image)
                ? asset('storage/' . $product->image)
                : ($featuredImage
                    ? asset('storage/' . $featuredImage->image_path)
                    : asset('img/logo.png'));

            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => (float) $product->price,
                'image' => $imageUrl,
            ];
        }

        session()->put('cart', $cart);

        if ($this->expectsJson($request)) {
            return response()->json($this->cartPayload(
                $cart,
                'Produsul a fost adăugat în colecție.'
            ));
        }

        if ($request->boolean('redirect_to_checkout')) {
            return redirect()->route('checkout.index');
        }

        return redirect()->back()->with('success', 'Produsul a fost adăugat în colecție (coș).');
    }

    /**
     * Elimină un produs din coșul de cumpărături.
     */
    public function remove(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
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

    private function cartPayload(array $cart, string $message): array
    {
        return [
            'success' => true,
            'message' => $message,
            'cart_count' => count($cart),
            'html' => view('cart._sidebar_content')->render(),
        ];
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
