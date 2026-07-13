<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * După plata reușită (din pagina Stripe).
     * Doar curăță sesiunea și afișează pagina de succes.
     */
    public function success(Request $request)
    {
        // Golim coșul temporal din sesiune, logica de comanda fiind tratată asincron prin webhook
        session()->forget('cart');

        return view('checkout.success');
    }

    /**
     * Dacă plata este anulată (din pagina Stripe).
     */
    public function cancel()
    {
        return redirect()->route('cart.index')->with('error', 'Plata a fost anulată. Puteți relua procesul oricând.');
    }
}
