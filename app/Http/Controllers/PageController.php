<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Pagina de start (Home Page)
     */
    public function home()
    {
        // 1. Luăm cele 3 categorii mari pentru secțiunea de navigare rapidă
        // Blaturi, Obiecte de Cult, Comemorative
        $featuredCategories = Category::whereIn('slug', ['blaturi-rasina', 'obiecte-de-cult', 'comemorative-animale'])
            ->take(3)
            ->get();

        // 2. Luăm ultimele 6 produse publicate pentru secțiunea "Noutăți în Galerie"
        $latestProducts = Product::where('status', 'published')
            ->with('images')
            ->latest()
            ->take(6)
            ->get();

        // 3. Luăm cele mai recente 3 articole de blog pentru secțiunea "Jurnal de Atelier"
        $latestPosts = Post::whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest()
            ->take(3)
            ->get();

        return view('home', compact('featuredCategories', 'latestProducts', 'latestPosts'));
    }

    /**
     * Pagina Despre Noi / Povestea Ivory Vintage
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Pagina de Contact
     */
    public function contact()
    {
        return view('pages.contact');
    }
}
