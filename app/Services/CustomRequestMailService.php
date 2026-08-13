<?php

namespace App\Services;

use App\Jobs\SendCustomRequestAcknowledgementEmail;
use App\Jobs\SendNewCustomRequestNotificationEmail;
use App\Models\CustomRequest;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Log;

class CustomRequestMailService
{
    public function queueNotifications(CustomRequest $customRequest): void
    {
        $customRequest->loadMissing('product');

        try {
            SendCustomRequestAcknowledgementEmail::dispatch($customRequest->id);
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
            SendNewCustomRequestNotificationEmail::dispatch($customRequest->id, $recipient);
        } catch (\Throwable $exception) {
            Log::error('Custom request admin notification could not be queued.', [
                'custom_request_id' => $customRequest->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
