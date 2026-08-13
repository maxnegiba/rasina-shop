<?php

namespace Tests\Feature;

use App\Services\BrevoTransactionalMailService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BrevoTransactionalMailServiceTest extends TestCase
{
    public function test_it_sends_transactional_email_with_sender_reply_to_and_attachment(): void
    {
        config()->set('services.brevo.api_key', 'test-api-key');
        config()->set('services.brevo.endpoint', 'https://api.brevo.com/v3/smtp/email');
        config()->set('services.brevo.sender_email', 'contact@mtdart.ro');
        config()->set('services.brevo.sender_name', 'MTD Art');
        config()->set('services.brevo.reply_to_email', 'owner@example.com');
        config()->set('services.brevo.reply_to_name', 'MTD Art');

        Http::fake([
            'https://api.brevo.com/*' => Http::response(['messageId' => '<test@brevo>'], 201),
        ]);

        $messageId = app(BrevoTransactionalMailService::class)->send(
            to: ['client@example.com'],
            subject: 'Test MTD ART',
            htmlContent: '<p>Salut</p>',
            attachments: [[
                'name' => 'test.txt',
                'content' => base64_encode('test'),
            ]],
            tags: ['test'],
        );

        $this->assertSame('<test@brevo>', $messageId);

        Http::assertSent(function (Request $request): bool {
            $payload = $request->data();

            return $request->url() === 'https://api.brevo.com/v3/smtp/email'
                && $request->hasHeader('api-key', 'test-api-key')
                && data_get($payload, 'sender.email') === 'contact@mtdart.ro'
                && data_get($payload, 'to.0.email') === 'client@example.com'
                && data_get($payload, 'replyTo.email') === 'owner@example.com'
                && data_get($payload, 'attachment.0.name') === 'test.txt';
        });
    }
}
