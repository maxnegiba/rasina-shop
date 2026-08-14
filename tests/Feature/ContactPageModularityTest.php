<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContactPageModularityTest extends TestCase
{
    public function test_contact_page_exposes_visibility_controls_and_custom_sections(): void
    {
        $settingsPage = file_get_contents(app_path('Filament/Pages/ManageGeneralSettings.php'));
        $settingsClass = file_get_contents(app_path('Settings/GeneralSettings.php'));
        $contactView = file_get_contents(resource_path('views/pages/contact.blade.php'));

        $this->assertIsString($settingsPage);
        $this->assertIsString($settingsClass);
        $this->assertIsString($contactView);

        $this->assertStringContainsString("CheckboxList::make('contact_enabled_sections')", $settingsPage);
        $this->assertStringContainsString("Repeater::make('contact_custom_sections')", $settingsPage);
        $this->assertStringContainsString('public array $contact_enabled_sections;', $settingsClass);
        $this->assertStringContainsString('public array $contact_custom_sections;', $settingsClass);

        foreach ([
            'header',
            'intro',
            'address',
            'communication',
            'hours',
            'custom_card',
            'contact_form',
            'custom_request',
        ] as $section) {
            $this->assertStringContainsString("\$isEnabled('{$section}')", $contactView);
        }

        $this->assertStringContainsString("'after_header'", $contactView);
        $this->assertStringContainsString("'after_main'", $contactView);
        $this->assertStringContainsString("'before_custom_request'", $contactView);
        $this->assertStringContainsString("'after_custom_request'", $contactView);
    }
}
