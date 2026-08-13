<?php

namespace Tests\Feature;

use App\Jobs\SendContactMessageEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_contact_message_is_queued_for_the_shop(): void
    {
        Queue::fake();
        config()->set('shop.legal.email', 'atelier@example.com');

        $this->post(route('contact.submit'), [
            'name' => 'Client Test',
            'email' => 'client@example.com',
            'subject' => 'Întrebare produs',
            'message' => 'Aș dori mai multe detalii.',
        ])->assertRedirect()->assertSessionHas('success');

        Queue::assertPushed(SendContactMessageEmail::class, function (SendContactMessageEmail $job): bool {
            return $job->recipient === 'atelier@example.com'
                && $job->messageData['email'] === 'client@example.com';
        });
    }
}
