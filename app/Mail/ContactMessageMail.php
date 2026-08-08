<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @param array{name: string, email: string, subject?: string|null, message: string} $messageData */
    public function __construct(public array $messageData)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->messageData['email'], $this->messageData['name'])],
            subject: 'Mesaj site: '.(($this->messageData['subject'] ?? null) ?: 'Solicitare nouă'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact_message');
    }
}
