<?php

namespace Tests\Feature;

use App\Mail\CustomRequestReceivedMail;
use App\Mail\NewCustomRequestMail;
use App\Models\CustomRequest;
use App\Services\CustomRequestMailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CustomRequestMailServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_and_shop_are_notified_for_a_custom_request(): void
    {
        Mail::fake();
        config()->set('shop.legal.email', 'atelier@example.com');

        $customRequest = CustomRequest::create([
            'customer_name' => 'Client Test',
            'customer_email' => 'client@example.com',
            'special_message' => 'Doresc o piesă asemănătoare.',
            'status' => 'new',
        ]);

        app(CustomRequestMailService::class)->queueNotifications($customRequest);

        Mail::assertQueued(CustomRequestReceivedMail::class, fn (CustomRequestReceivedMail $mail): bool =>
            $mail->hasTo('client@example.com')
        );

        Mail::assertQueued(NewCustomRequestMail::class, fn (NewCustomRequestMail $mail): bool =>
            $mail->hasTo('atelier@example.com')
        );
    }
}
