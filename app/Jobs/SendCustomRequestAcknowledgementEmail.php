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

class SendCustomRequestAcknowledgementEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $customRequestId)
    {
    }

    public function handle(BrevoTransactionalMailService $brevo): void
    {
        $customRequest = CustomRequest::query()->with('product')->findOrFail($this->customRequestId);

        $brevo->send(
            to: [[
                'email' => $customRequest->customer_email,
                'name' => $customRequest->customer_name,
            ]],
            subject: 'Am primit cererea ta - '.config('shop.brand_name'),
            htmlContent: view('emails.custom_request_received', ['customRequest' => $customRequest])->render(),
            tags: ['custom-request-customer'],
        );
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Custom request acknowledgement email failed.', [
            'custom_request_id' => $this->customRequestId,
            'exception' => $exception->getMessage(),
        ]);
    }
}
