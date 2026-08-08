<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_contact_message_is_queued_for_the_shop(): void
    {
        Mail::fake();
        config()->set('shop.legal.email', 'atelier@example.com');

        $this->post(route('contact.submit'), [
            'name' => 'Client Test',
            'email' => 'client@example.com',
            'subject' => 'Întrebare produs',
            'message' => 'Aș dori mai multe detalii.',
        ])->assertRedirect()->assertSessionHas('success');

        Mail::assertQueued(ContactMessageMail::class, function (ContactMessageMail $mail): bool {
            return $mail->hasTo('atelier@example.com')
                && $mail->messageData['email'] === 'client@example.com';
        });
    }
}
