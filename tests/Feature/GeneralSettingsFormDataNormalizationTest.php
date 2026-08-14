<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageGeneralSettings;
use Tests\TestCase;

class GeneralSettingsFormDataNormalizationTest extends TestCase
{
    public function test_optional_blank_values_are_normalized_before_typed_settings_are_filled(): void
    {
        $page = new class extends ManageGeneralSettings
        {
            public function normalizeForTest(array $data): array
            {
                return $this->mutateFormDataBeforeSave($data);
            }
        };

        $data = $page->normalizeForTest([
            'contact_phone' => null,
            'facebook_url' => null,
            'about_image' => null,
            'about_image_alt' => null,
            'about_title' => 'Arta Născută din Pasiune',
            'working_hours' => null,
        ]);

        $this->assertSame('', $data['contact_phone']);
        $this->assertSame('', $data['facebook_url']);
        $this->assertSame('', $data['about_image']);
        $this->assertSame('', $data['about_image_alt']);
        $this->assertSame('Arta Născută din Pasiune', $data['about_title']);
        $this->assertSame([], $data['working_hours']);
    }
}
