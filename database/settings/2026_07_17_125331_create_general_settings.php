<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.contact_whatsapp_number', '');
        $this->migrator->add('general.contact_phone', '');
        $this->migrator->add('general.contact_email', '');
        $this->migrator->add('general.company_address', '');
        $this->migrator->add('general.facebook_url', '');
        $this->migrator->add('general.instagram_url', '');
        $this->migrator->add('general.default_whatsapp_greeting_text', 'Salut, aș dori o ofertă pentru un produs personalizat.');
    }
};
