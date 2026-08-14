<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminMfaCodeMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public string $code,
        public int $expiresInMinutes = 10,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cod de securitate - '.config('shop.brand_name', 'MTD Art'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin_mfa_code');
    }
}
