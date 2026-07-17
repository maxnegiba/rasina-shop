<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $contact_whatsapp_number;
    public string $contact_phone;
    public string $contact_email;
    public string $company_address;
    public string $facebook_url;
    public string $instagram_url;
    public string $default_whatsapp_greeting_text;

    public static function group(): string
    {
        return 'general';
    }
}
