<?php

namespace App\Mail;

use App\Models\CustomRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class NewCustomRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public CustomRequest $customRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->customRequest->customer_email, $this->customRequest->customer_name)],
            subject: 'Cerere personalizată nouă - '.config('shop.brand_name'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new_custom_request');
    }
}
