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
    public array $working_hours;

    public array $contact_enabled_sections;
    public array $contact_custom_sections;

    public string $contact_eyebrow;
    public string $contact_title;
    public string $contact_intro;
    public string $contact_address_label;
    public string $contact_address_note;
    public string $contact_communication_label;
    public string $contact_hours_label;
    public string $contact_custom_card_title;
    public string $contact_custom_card_text;
    public string $contact_custom_card_cta;
    public string $contact_form_title;
    public string $contact_form_response_note;
    public string $contact_custom_eyebrow;
    public string $contact_custom_title;
    public string $contact_custom_intro;

    public string $about_eyebrow;
    public string $about_title;
    public string $about_image;
    public string $about_image_alt;
    public string $about_quote;
    public string $about_paragraph_one;
    public string $about_paragraph_two;
    public string $about_value_one_title;
    public string $about_value_one_text;
    public string $about_value_two_title;
    public string $about_value_two_text;
    public string $about_cta_label;

    public static function group(): string
    {
        return 'general';
    }
}
