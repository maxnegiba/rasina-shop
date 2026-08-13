<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailSmokeTestCommandTest extends TestCase
{
    public function test_mail_smoke_test_accepts_a_valid_recipient(): void
    {
        Mail::fake();

        $this->artisan('mtd:mail-test', ['to' => 'recipient@example.com'])
            ->expectsOutputToContain('Emailul de test a fost acceptat')
            ->assertSuccessful();
    }

    public function test_mail_smoke_test_rejects_an_invalid_recipient(): void
    {
        Mail::fake();

        $this->artisan('mtd:mail-test', ['to' => 'not-an-email'])
            ->expectsOutputToContain('nu este o adresă de email validă')
            ->assertFailed();
    }
}
