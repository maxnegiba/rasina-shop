<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProformaController extends Controller
{
    /**
     * Generează și returnează PDF-ul pentru proforma.
     */
    public function download(Order $order)
    {
        // Ne asigurăm că există un număr de proformă. Dacă nu (ex: comenzi vechi), generăm unul.
        if (empty($order->proforma_number)) {
            $order->update(['proforma_number' => Order::generateProformaNumber()]);
        }

        // Încarcă relațiile necesare
        $order->load('items.product');

        // Generăm PDF-ul din view
        $pdf = Pdf::loadView('pdf.proforma', compact('order'));

        // Setăm numele fișierului
        $filename = 'Proforma_' . $order->proforma_number . '_' . $order->order_number . '.pdf';

        return $pdf->download($filename);
    }
}
