<?php

namespace Tests\Feature;

use App\Mail\AdminMfaCodeMail;
use App\Mail\ContactMessageMail;
use App\Mail\CustomRequestReceivedMail;
use App\Mail\NewCustomRequestMail;
use App\Mail\OrderConfirmationMail;
use App\Models\CustomRequest;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandedMailRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_and_admin_mail_templates_render_with_mtd_art_branding(): void
    {
        $order = Order::query()->create([
            'order_number' => 'MTD-TEST-001',
            'subtotal_amount' => 125,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 125,
            'payment_status' => 'paid',
            'shipping_status' => 'processing',
            'customer_details' => [
                'name' => 'Ana Popescu',
                'email' => 'ana@example.com',
                'phone' => '0700000000',
            ],
            'proforma_number' => 'PROFORMA-2026-000001',
        ]);

        $request = CustomRequest::query()->create([
            'customer_name' => 'Ana Popescu',
            'customer_email' => 'ana@example.com',
            'customer_phone' => '0700000000',
            'dimensions_requested' => '30 × 20 cm',
            'color_preferences' => 'Ivory și auriu',
            'special_message' => 'Îmi doresc o piesă luminoasă.',
            'status' => 'new',
        ]);

        $mails = [
            new OrderConfirmationMail($order),
            new ContactMessageMail([
                'name' => 'Ana Popescu',
                'email' => 'ana@example.com',
                'subject' => 'Întrebare',
                'message' => 'Bună ziua!',
            ]),
            new CustomRequestReceivedMail($request),
            new NewCustomRequestMail($request),
            new AdminMfaCodeMail('123456', 10),
        ];

        foreach ($mails as $mail) {
            $html = $mail->render();

            $this->assertStringContainsString('MTD ART', $html);
            $this->assertStringContainsString('img/logo.png', $html);
            $this->assertStringContainsString('#2c1e16', $html);
            $this->assertStringContainsString('#cfb53b', $html);
        }
    }
}
