<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CustomRequestController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Ivory Vintage Art Gallery
|--------------------------------------------------------------------------
*/

// --- Pagini Statice & Informaționale ---
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/despre-noi', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// --- Magazin (Shop) ---
Route::group(['prefix' => 'magazin', 'as' => 'shop.'], function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/categorie/{slug}', [ShopController::class, 'category'])->name('category');
    Route::get('/produs/{slug}', [ShopController::class, 'show'])->name('show');
});

// --- Cereri Personalizate (Custom Requests) ---
// Aici trimitem datele din formularul pentru produse unicat
Route::post('/cerere-personalizata', [CustomRequestController::class, 'store'])->name('custom-request.store');

// --- Jurnal de Atelier (Blog) ---
Route::group(['prefix' => 'jurnal', 'as' => 'blog.'], function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

// --- Checkout & Plăți (Stripe) ---
// Aceste rute le vom activa complet când facem CheckoutController
Route::group(['prefix' => 'checkout', 'as' => 'checkout.'], function () {
    Route::post('/create-session', [CheckoutController::class, 'createSession'])->name('session');
    Route::get('/succes', [CheckoutController::class, 'success'])->name('success');
    Route::get('/anulare', [CheckoutController::class, 'cancel'])->name('cancel');
});

// Notă: Rutele pentru Filament (Admin) sunt gestionate automat de pachet, 
// deci nu trebuie să adăugăm nimic aici pentru panoul de control.
