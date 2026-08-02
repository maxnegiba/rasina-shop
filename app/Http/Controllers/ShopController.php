<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class ShopController extends Controller
{
    /**
     * Afișează pagina principală a magazinului (toate produsele disponibile).
     */
    public function index()
    {
        $products = Product::where('status', 'published')
            ->where('stock', '>', 0)
            ->with('images')
            ->latest()
            ->paginate(12);

        $categories = $this->categoriesWithAvailabilityCounts();

        return view('shop.index', compact('products', 'categories'));
    }

    /**
     * Afișează separat produsele disponibile și produsele vândute dintr-o categorie.
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->where('status', 'published')
            ->where('stock', '>', 0)
            ->with('images')
            ->latest()
            ->paginate(12);

        $soldProducts = Product::where('category_id', $category->id)
            ->where('status', 'published')
            ->where('stock', '<=', 0)
            ->with('images')
            ->latest()
            ->get();

        $categories = $this->categoriesWithAvailabilityCounts();

        return view('shop.index', compact('products', 'soldProducts', 'category', 'categories'));
    }

    /**
     * Afișează pagina unui singur produs.
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'published')
            ->with('images')
            ->firstOrFail();

        $featuredImage = $product->images->where('is_featured', true)->first()
                         ?? $product->images->first();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'published')
            ->where('stock', '>', 0)
            ->with('images')
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'featuredImage', 'relatedProducts'));
    }

    /**
     * Contorizează separat piesele disponibile și cele vândute pentru navigarea pe categorii.
     */
    private function categoriesWithAvailabilityCounts()
    {
        return Category::withCount([
            'products as products_count' => fn ($query) => $query
                ->where('status', 'published')
                ->where('stock', '>', 0),
            'products as sold_products_count' => fn ($query) => $query
                ->where('status', 'published')
                ->where('stock', '<=', 0),
        ])->get();
    }
}
