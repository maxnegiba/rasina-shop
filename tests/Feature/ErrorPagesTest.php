<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_page_uses_the_customer_facing_error_screen(): void
    {
        $this->get('/pagina-care-nu-exista')
            ->assertNotFound()
            ->assertSee('Pagina nu a fost găsită')
            ->assertDontSee('Stack trace');
    }
}
