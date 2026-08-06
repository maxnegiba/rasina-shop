<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_legal_pages_are_seeded_and_public(): void
    {
        $slugs = [
            'termeni-si-conditii',
            'politica-de-confidentialitate',
            'politica-de-retur',
        ];

        $this->assertSame(3, Page::whereIn('slug', $slugs)->count());

        foreach ($slugs as $slug) {
            $this->get(route('page.show', $slug))
                ->assertOk()
                ->assertSee(Page::where('slug', $slug)->firstOrFail()->title);
        }
    }
}
