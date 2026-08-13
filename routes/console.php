<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mtd:mail-test {to}', function (string $to) {
    if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $this->error('Destinatarul nu este o adresă de email validă.');

        return self::FAILURE;
    }

    try {
        Mail::raw(
            'Test de livrare email MTD Art prin transportul configurat în Laravel.',
            fn ($message) => $message
                ->to($to)
                ->subject('Test email MTD Art'),
        );
    } catch (Throwable $exception) {
        $this->error('Trimiterea a eșuat: '.$exception->getMessage());

        return self::FAILURE;
    }

    $this->info('Emailul de test a fost acceptat de transportul configurat. Verifică inboxul și logurile Brevo.');

    return self::SUCCESS;
})->purpose('Trimite un email de test prin transportul configurat pentru MTD Art');

Schedule::command('checkout:release-expired-reservations')
    ->everyFiveMinutes()
    ->withoutOverlapping();
