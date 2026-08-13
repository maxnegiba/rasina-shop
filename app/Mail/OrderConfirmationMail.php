<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmare comandă '.$this->order->order_number.' - '.config('shop.brand_name'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order_confirmation');
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn (): string => Pdf::loadView('pdf.proforma', [
                    'order' => $this->order->loadMissing('items.product'),
                ])->output(),
                'Proforma_'.$this->order->proforma_number.'.pdf',
            )
                ->as('Proforma_'.$this->order->proforma_number.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
