<?php

namespace App\Http\Controllers;

use App\Models\CustomRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomRequestController extends Controller
{
    /**
     * Procesează trimiterea formularului de cerere personalizată
     */
    public function store(Request $request)
    {
        // 1. Validarea datelor (Siguranță înainte de toate)
        $validatedData = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'dimensions_requested' => 'nullable|string|max:255',
            'color_preferences' => 'nullable|string|max:255',
            'special_message' => 'nullable|string',
            'reference_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB
        ], [
            // Mesaje de eroare personalizate în română
            'customer_name.required' => 'Te rugăm să ne spui cum te numiești.',
            'customer_email.required' => 'Avem nevoie de adresa ta de email pentru a-ți trimite oferta.',
            'customer_email.email' => 'Adresa de email nu pare a fi validă.',
            'reference_image.image' => 'Fișierul încărcat trebuie să fie o imagine.',
            'reference_image.max' => 'Imaginea este prea mare (maxim 5MB).',
        ]);

        // 2. Gestionarea imaginii încărcate
        $imagePath = null;
        if ($request->hasFile('reference_image')) {
            // Salvăm imaginea în folderul 'custom_requests' din disk-ul public
            $imagePath = $request->file('reference_image')->store('custom_requests', 'public');
        }

        // 3. Crearea înregistrării în Baza de Date
        $customRequest = CustomRequest::create([
            'product_id' => $validatedData['product_id'],
            'customer_name' => $validatedData['customer_name'],
            'customer_email' => $validatedData['customer_email'],
            'customer_phone' => $validatedData['customer_phone'],
            'dimensions_requested' => $validatedData['dimensions_requested'],
            'color_preferences' => $validatedData['color_preferences'],
            'special_message' => $validatedData['special_message'],
            'reference_image_path' => $imagePath,
            'status' => 'new', // Apare automat ca "Nouă" în Filament
        ]);

        // 4. Feedback pentru client
        // Redirecționăm înapoi cu un mesaj de succes care va fi afișat elegant în site
        return redirect()->back()->with('success', 'Cererea ta a fost trimisă cu succes! Te vom contacta în cel mai scurt timp pentru a discuta detaliile și oferta de preț.');
    }
}
