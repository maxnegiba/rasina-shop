<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Afișează pagina principală a magazinului (Toate produsele)
     */
    public function index()
    {
        // Aducem doar produsele publicate, ordonate de la cele mai noi, cu paginare
        $products = Product::where('status', 'published')
            ->with('images') // Pre-încărcăm imaginile pentru a optimiza viteza (evităm N+1 queries)
            ->latest()
            ->paginate(12);

        $categories = Category::withCount('products')->get();

        return view('shop.index', compact('products', 'categories'));
    }

    /**
     * Afișează produsele dintr-o anumită categorie
     */
    public function category($slug)
    {
        // Găsim categoria pe baza link-ului (slug)
        $category = Category::where('slug', $slug)->firstOrFail();

        // Aducem produsele doar din această categorie
        $products = Product::where('category_id', $category->id)
            ->where('status', 'published')
            ->with('images')
            ->latest()
            ->paginate(12);

        $categories = Category::withCount('products')->get();

        return view('shop.index', compact('products', 'category', 'categories'));
    }

    /**
     * Afișează pagina unui singur produs (Aici se întâmplă magia Standard vs Unicat)
     */
    public function show($slug)
    {
        // Găsim produsul după link. Folosim firstOrFail ca să dea eroare 404 dacă nu există.
        $product = Product::where('slug', $slug)
            ->where('status', 'published')
            ->with('images')
            ->firstOrFail();

        // Extragem imaginea principală (dacă există), altfel prima imagine din galerie
        $featuredImage = $product->images->where('is_featured', true)->first() 
                         ?? $product->images->first();

        // Recomandări: Aducem alte 4 produse din aceeași categorie pentru "S-ar putea să-ți placă și..."
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id) // Excludem produsul curent
            ->where('status', 'published')
            ->with('images')
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Returnăm view-ul și îi pasăm produsul. 
        // În fișierul Blade (design), vom face o simplă verificare: if($product->is_custom) { arată formularul } else { arată butonul Adaugă în coș }
        return view('shop.show', compact('product', 'featuredImage', 'relatedProducts'));
    }
}
