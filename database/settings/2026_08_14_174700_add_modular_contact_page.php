<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.contact_enabled_sections', [
            'header',
            'intro',
            'address',
            'communication',
            'hours',
            'custom_card',
            'contact_form',
            'custom_request',
        ]);

        $this->migrator->add('general.contact_custom_sections', []);
    }
};
