<?php

namespace App\Http\Controllers;

use App\Models\CustomRequest;
use App\Services\CustomRequestMailService;
use App\Services\PrivateImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CustomRequestController extends Controller
{
    /**
     * Procesează trimiterea formularului de cerere personalizată.
     */
    public function store(
        Request $request,
        PrivateImageUploadService $images,
        CustomRequestMailService $mail,
    ): RedirectResponse {
        $validatedData = $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'dimensions_requested' => ['nullable', 'string', 'max:255'],
            'color_preferences' => ['nullable', 'string', 'max:255'],
            'special_message' => ['nullable', 'string', 'max:5000'],
            'reference_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'],
        ], [
            'customer_name.required' => 'Te rugăm să ne spui cum te numiești.',
            'customer_email.required' => 'Avem nevoie de adresa ta de email pentru a-ți trimite oferta.',
            'customer_email.email' => 'Adresa de email nu pare a fi validă.',
            'reference_image.image' => 'Fișierul încărcat trebuie să fie o imagine.',
            'reference_image.max' => 'Imaginea este prea mare (maxim 5MB).',
            'reference_image.dimensions' => 'Imaginea are o rezoluție prea mare (maxim 6000 × 6000 px).',
        ]);

        $imagePath = null;

        if ($request->hasFile('reference_image')) {
            try {
                $imagePath = $images->store($request->file('reference_image'));
            } catch (RuntimeException $exception) {
                throw ValidationException::withMessages([
                    'reference_image' => $exception->getMessage(),
                ]);
            }
        }

        $customRequest = CustomRequest::create([
            'product_id' => $validatedData['product_id'] ?? null,
            'customer_name' => $validatedData['customer_name'],
            'customer_email' => $validatedData['customer_email'],
            'customer_phone' => $validatedData['customer_phone'] ?? null,
            'dimensions_requested' => $validatedData['dimensions_requested'] ?? null,
            'color_preferences' => $validatedData['color_preferences'] ?? null,
            'special_message' => $validatedData['special_message'] ?? null,
            'reference_image_path' => $imagePath,
            'status' => 'new',
        ]);

        $mail->queueNotifications($customRequest);

        return redirect()->back()->with('success', 'Cererea ta a fost trimisă cu succes! Te vom contacta în cel mai scurt timp pentru a discuta detaliile și oferta de preț.');
    }
}
