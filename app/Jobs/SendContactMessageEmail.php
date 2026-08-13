<?php

namespace App\Jobs;

use App\Services\BrevoTransactionalMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendContactMessageEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @param array{name: string, email: string, subject?: string|null, message: string} $messageData */
    public function __construct(public array $messageData, public string $recipient)
    {
    }

    public function handle(BrevoTransactionalMailService $brevo): void
    {
        $brevo->send(
            to: [$this->recipient],
            subject: 'Mesaj site: '.(($this->messageData['subject'] ?? null) ?: 'Solicitare nouă'),
            htmlContent: view('emails.contact_message', ['messageData' => $this->messageData])->render(),
            replyTo: [
                'email' => $this->messageData['email'],
                'name' => $this->messageData['name'],
            ],
            tags: ['contact-form'],
        );
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Contact form email failed.', [
            'recipient' => $this->recipient,
            'exception' => $exception->getMessage(),
        ]);
    }
}
