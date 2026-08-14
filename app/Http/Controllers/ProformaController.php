<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class ProformaController extends Controller
{
    /**
     * Generează și returnează PDF-ul pentru proforma.
     */
    public function download(Order $order): Response
    {
        abort_unless($order->payment_status === 'paid' || $order->isCashOnDelivery(), 404);

        if (! $order->proforma_number) {
            $order->update(['proforma_number' => $order->proformaNumber()]);
        }

        $order->load('items.product');
        $pdf = Pdf::loadView('pdf.proforma', compact('order'));
        $filename = 'Proforma_'.$order->proforma_number.'_'.$order->order_number.'.pdf';

        return $pdf->download($filename);
    }
}
