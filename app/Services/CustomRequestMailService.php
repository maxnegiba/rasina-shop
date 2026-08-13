<?php

namespace App\Services;

use App\Mail\CustomRequestReceivedMail;
use App\Mail\NewCustomRequestMail;
use App\Models\CustomRequest;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomRequestMailService
{
    public function queueNotifications(CustomRequest $customRequest): void
    {
        $customRequest->loadMissing('product');

        try {
            Mail::to($customRequest->customer_email)
                ->queue(new CustomRequestReceivedMail($customRequest));
        } catch (\Throwable $exception) {
            Log::error('Custom request acknowledgement could not be queued.', [
                'custom_request_id' => $customRequest->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        $recipient = app(GeneralSettings::class)->contact_email ?: config('shop.legal.email');

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            Log::error('Custom request admin notification has no valid recipient.', [
                'custom_request_id' => $customRequest->id,
            ]);

            return;
        }

        try {
            Mail::to($recipient)->queue(new NewCustomRequestMail($customRequest));
        } catch (\Throwable $exception) {
            Log::error('Custom request admin notification could not be queued.', [
                'custom_request_id' => $customRequest->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
