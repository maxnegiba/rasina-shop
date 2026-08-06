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

    public function test_checkout_view_renders_its_payment_button_label(): void
    {
        $this->withoutVite();

        $order = (object) [
            'items' => collect(),
            'subtotal_amount' => 123.45,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 123.45,
        ];

        $html = view('checkout.index', [
            'clientSecret' => 'pi_test_secret_test',
            'stripeKey' => 'pk_test_example',
            'orderToken' => '00000000-0000-4000-8000-000000000000',
            'totalAmount' => 123.45,
            'order' => $order,
        ])->render();

        $this->assertStringContainsString('Plătește 123,45 RON', $html);
        $this->assertStringContainsString('const paymentButtonLabel = ', $html);
        $this->assertStringNotContainsString('@json(', $html);
    }
}
