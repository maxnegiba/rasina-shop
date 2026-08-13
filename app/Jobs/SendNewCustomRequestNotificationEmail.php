<?php

namespace App\Jobs;

use App\Models\CustomRequest;
use App\Services\BrevoTransactionalMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNewCustomRequestNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $customRequestId, public string $recipient)
    {
    }

    public function handle(BrevoTransactionalMailService $brevo): void
    {
        $customRequest = CustomRequest::query()->with('product')->findOrFail($this->customRequestId);

        $brevo->send(
            to: [$this->recipient],
            subject: 'Cerere personalizată nouă - '.config('shop.brand_name'),
            htmlContent: view('emails.new_custom_request', ['customRequest' => $customRequest])->render(),
            replyTo: [
                'email' => $customRequest->customer_email,
                'name' => $customRequest->customer_name,
            ],
            tags: ['custom-request-admin'],
        );
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Custom request admin notification email failed.', [
            'custom_request_id' => $this->customRequestId,
            'exception' => $exception->getMessage(),
        ]);
    }
}
