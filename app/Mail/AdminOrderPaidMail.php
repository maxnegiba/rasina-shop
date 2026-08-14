<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOrderPaidMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ($this->order->isCashOnDelivery() ? 'Comandă ramburs ' : 'Comandă plătită ')
                .$this->order->order_number.' - '.config('shop.brand_name'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin_order_paid');
    }
}
