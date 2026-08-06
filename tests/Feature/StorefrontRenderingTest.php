<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_layout_does_not_expose_uncompiled_blade_source(): void
    {
        $this->withoutVite();

        $this->get(route('shop.index'))
            ->assertOk()
            ->assertDontSee('sum(fn', false)
            ->assertDontSee('@if', false)
            ->assertDontSee('@yield', false)
            ->assertDontSee('@php', false)
            ->assertDontSee('{{', false);
    }
}
