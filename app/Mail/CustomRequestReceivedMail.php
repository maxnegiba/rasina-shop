<?php

namespace App\Mail;

use App\Models\CustomRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomRequestReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public CustomRequest $customRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Am primit cererea ta - '.config('shop.brand_name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.custom_request_received');
    }
}
