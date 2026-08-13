<?php

namespace Tests\Feature;

use App\Jobs\SendCustomRequestAcknowledgementEmail;
use App\Jobs\SendNewCustomRequestNotificationEmail;
use App\Models\CustomRequest;
use App\Services\CustomRequestMailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CustomRequestMailServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_and_shop_are_notified_for_a_custom_request(): void
    {
        Queue::fake();
        config()->set('shop.legal.email', 'atelier@example.com');

        $customRequest = CustomRequest::create([
            'customer_name' => 'Client Test',
            'customer_email' => 'client@example.com',
            'special_message' => 'Doresc o piesă asemănătoare.',
            'status' => 'new',
        ]);

        app(CustomRequestMailService::class)->queueNotifications($customRequest);

        Queue::assertPushed(SendCustomRequestAcknowledgementEmail::class, fn (SendCustomRequestAcknowledgementEmail $job): bool =>
            $job->customRequestId === $customRequest->id
        );

        Queue::assertPushed(SendNewCustomRequestNotificationEmail::class, fn (SendNewCustomRequestNotificationEmail $job): bool =>
            $job->customRequestId === $customRequest->id && $job->recipient === 'atelier@example.com'
        );
    }
}
