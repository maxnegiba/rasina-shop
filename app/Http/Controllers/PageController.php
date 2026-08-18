<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\Product;
use App\Models\Post;
use App\Models\Category;
use App\Models\Page;
use App\Services\MarketingDataLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        // Homepage keeps one complete desktop row. This reduces below-the-fold image
        // payload while preserving a useful recent-work preview.
        $latestProducts = Product::where('status', 'published')
            ->where('stock', '>', 0)
            ->with('images')
            ->latest()
            ->take(4)
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

    /**
     * Trimite mesajul din formularul generic de contact
     */
    public function submitContact(Request $request, MarketingDataLayer $dataLayer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $recipient = app(\App\Settings\GeneralSettings::class)->contact_email
            ?: config('shop.legal.email');

        try {
            Mail::to($recipient)->queue(new ContactMessageMail($validated));
        } catch (\Throwable $exception) {
            Log::error('Contact message could not be queued.', [
                'exception' => $exception->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Mesajul nu a putut fi trimis momentan. Încercați din nou sau folosiți datele de contact afișate.');
        }

        $dataLayer->flashPush('contact_form_sent', [
            'contact' => [
                'source' => 'contact_page',
            ],
        ]);

        return redirect()->back()->with('success', 'Vă mulțumim pentru mesaj! Vă vom contacta în curând.');
    }

    /**
     * Pagina dinamica legala/informationala
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
