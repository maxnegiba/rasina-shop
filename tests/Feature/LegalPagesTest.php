<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    private const LEGAL_SLUGS = [
        'termeni-si-conditii',
        'politica-de-confidentialitate',
        'politica-de-retur',
    ];

    private const LEGAL_PLACEHOLDERS = [
        '[DENUMIREA LEGALĂ A OPERATORULUI]',
        '[CUI/CIF]',
        '[NR. REGISTRUL COMERȚULUI]',
        '[ADRESA SEDIULUI SOCIAL]',
        '[TELEFON]',
    ];

    public function test_footer_legal_pages_are_seeded_and_public(): void
    {
        $this->assertSame(3, Page::whereIn('slug', self::LEGAL_SLUGS)->count());

        foreach (self::LEGAL_SLUGS as $slug) {
            $this->get(route('page.show', $slug))
                ->assertOk()
                ->assertSee(Page::where('slug', $slug)->firstOrFail()->title);
        }
    }

    public function test_public_legal_pages_never_expose_seed_placeholders(): void
    {
        foreach (self::LEGAL_SLUGS as $slug) {
            $response = $this->get(route('page.show', $slug))->assertOk();

            foreach (self::LEGAL_PLACEHOLDERS as $placeholder) {
                $response->assertDontSee($placeholder);
            }
        }
    }

    public function test_terms_page_contains_the_real_operator_identity(): void
    {
        $this->get(route('page.show', 'termeni-si-conditii'))
            ->assertOk()
            ->assertSee('MTD STUDIO PROFESSIONAL SRL')
            ->assertSee('52534613')
            ->assertSee('J2025071368008')
            ->assertSee('+40 771 768 582');
    }
}
