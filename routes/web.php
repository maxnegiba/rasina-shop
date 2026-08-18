<?php

use App\Http\Controllers\AdminMfaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CustomRequestController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\OptimizedImageController;
use App\Http\Middleware\TrackBeginCheckout;
use App\Http\Middleware\TrackPurchase;
use App\Services\MarketingDataLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Ivory Vintage Art Gallery
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/despre-noi', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])
    ->middleware('throttle:5,1')
    ->name('contact.submit');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/media/optimized/{encoded}', OptimizedImageController::class)
    ->where('encoded', '[A-Za-z0-9_-]+')
    ->name('media.optimized');

Route::group(['prefix' => 'magazin', 'as' => 'shop.'], function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/categorie/{slug}', [ShopController::class, 'category'])->name('category');
    Route::get('/produs/{slug}', [ShopController::class, 'show'])->name('show');
});

Route::post('/cerere-personalizata', [CustomRequestController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('custom-request.store');
Route::view('/custom-orders', 'custom-orders')->name('custom-orders');

Route::group(['prefix' => 'jurnal', 'as' => 'blog.'], function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

Route::group(['prefix' => 'cos', 'as' => 'cart.'], function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/adauga', [CartController::class, 'add'])->middleware('throttle:30,1')->name('add');
    Route::post('/actualizeaza', [CartController::class, 'update'])->middleware('throttle:30,1')->name('update');
    Route::post('/sterge', [CartController::class, 'remove'])->middleware('throttle:30,1')->name('remove');
});

Route::group(['prefix' => 'checkout', 'as' => 'checkout.'], function () {
    Route::get('/', [CheckoutController::class, 'index'])
        ->middleware(TrackBeginCheckout::class)
        ->name('index');
    Route::post('/acceptare', [CheckoutController::class, 'acceptTerms'])->middleware('throttle:30,1')->name('accept-terms');
    Route::post('/ramburs', [CheckoutController::class, 'cashOnDelivery'])->middleware('throttle:20,1')->name('cash-on-delivery');
    Route::get('/succes', [CheckoutController::class, 'success'])
        ->middleware(TrackPurchase::class)
        ->name('success');
    Route::post('/anulare', [CheckoutController::class, 'cancel'])->middleware('throttle:30,1')->name('cancel');
});

// Safe browser validation endpoints for the marketing staging environment only.
// They never create orders, reserve stock, or contact Stripe.
if (app()->environment('staging')) {
    Route::get('/_marketing/begin-checkout-preview', function (Request $request) {
        if ($request->boolean('reset')) {
            $request->session()->forget('marketing_begin_checkout_fingerprint');
        }

        return response(
            '<!doctype html><html><head><meta charset="utf-8"><title>MTD ART begin_checkout preview</title></head><body><p>Marketing staging preview.</p></body></html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    })->middleware(TrackBeginCheckout::class)->name('marketing.begin-checkout-preview');

    Route::get('/_marketing/purchase-preview', function (MarketingDataLayer $dataLayer) {
        $dataLayer->push('purchase', [
            'ecommerce' => [
                'transaction_id' => 'MTD-PREVIEW-20260818-01',
                'currency' => 'RON',
                'value' => 0,
                'shipping' => 0,
                'items' => [[
                    'item_id' => 'PREVIEW-001',
                    'item_name' => 'MTD ART Analytics Preview',
                    'price' => 0,
                    'quantity' => 1,
                ]],
            ],
        ]);

        return response(
            '<!doctype html><html><head><meta charset="utf-8"><title>MTD ART purchase preview</title></head><body><p>Marketing purchase preview. No order was created.</p></body></html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    })->name('marketing.purchase-preview');
}

Route::post('/webhook/stripe', [\App\Http\Controllers\WebhookController::class, 'handleStripeWebhook'])->name('webhook.stripe');

Route::get('/proforma/{order:public_token}', [\App\Http\Controllers\ProformaController::class, 'download'])
    ->middleware('signed')
    ->name('order.proforma.download');

Route::middleware('throttle:20,1')
    ->prefix('admin-security')
    ->name('admin.mfa.')
    ->group(function () {
        Route::get('/challenge', [AdminMfaController::class, 'show'])->name('challenge');
        Route::post('/verify', [AdminMfaController::class, 'verify'])->name('verify');
        Route::post('/resend', [AdminMfaController::class, 'resend'])->name('resend');
        Route::post('/logout', [AdminMfaController::class, 'logout'])->name('logout');
    });

Route::get('/info/{slug}', [PageController::class, 'show'])->name('page.show');
