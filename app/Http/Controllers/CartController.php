<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Afișează conținutul coșului de cumpărături.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cart', 'total'));
    }

    /**
     * Adaugă un produs în coșul de cumpărături.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'redirect_to_checkout' => 'nullable|boolean'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Dacă este un produs la comandă, redirecționăm către contact
        if ($product->is_custom) {
            return redirect()->route('contact')->with('success', 'Aceasta este o piesă unicat. Vă rugăm să ne lăsați un mesaj pentru o comandă.');
        }

        // Verificăm stocul
        if ($product->stock < 1) {
            return redirect()->back()->with('error', 'Produsul nu mai este în stoc.');
        }

        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        // Imaginea pentru coș
        $imageUrl = null;
        if (!empty($product->image)) {
            $imageUrl = asset('storage/' . $product->image);
        } elseif (isset($product->images) && $product->images->count() > 0) {
            $firstImage = $product->images->where('is_featured', true)->first() ?? $product->images->first();
            $imageUrl = asset('storage/' . $firstImage->image_path);
        } else {
            $imageUrl = 'https://via.placeholder.com/150'; // Fallback
        }

        // Dacă produsul este deja în coș, creștem cantitatea, verificând stocul maxim
        if (isset($cart[$product->id])) {
            $newQuantity = $cart[$product->id]['quantity'] + $quantity;
            if ($newQuantity > $product->stock) {
                 return redirect()->back()->with('error', 'Nu poți adăuga mai multe bucăți decât stocul disponibil.');
            }
            $cart[$product->id]['quantity'] = $newQuantity;
        } else {
            // Adăugăm produs nou în coș
            $cart[$product->id] = [
                "id" => $product->id,
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "image" => $imageUrl
            ];
        }

        session()->put('cart', $cart);

        if ($request->ajax() || $request->wantsJson()) {
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            return response()->json([
                'success' => true,
                'message' => 'Produsul a fost adăugat în colecție.',
                'cart_count' => count($cart),
                'html' => view('cart._sidebar_content')->render(),
            ]);
        }

        // Verificăm dacă a apăsat pe butonul "Buy Now"
        if ($request->input('redirect_to_checkout')) {
            return redirect()->route('checkout.session');
        }

        return redirect()->back()->with('success', 'Produsul a fost adăugat în colecție (coș).');
    }

    /**
     * Elimină un produs din coșul de cumpărături.
     */
    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');

            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }

            if ($request->ajax() || $request->wantsJson()) {
                $cart = session()->get('cart', []);
                $total = 0;
                foreach ($cart as $item) {
                    $total += $item['price'] * $item['quantity'];
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Produsul a fost eliminat din coș.',
                    'cart_count' => count($cart),
                    'html' => view('cart._sidebar_content')->render(),
                ]);
            }

            return redirect()->back()->with('success', 'Produsul a fost eliminat din coș.');
        }

        return redirect()->back();
    }
}
