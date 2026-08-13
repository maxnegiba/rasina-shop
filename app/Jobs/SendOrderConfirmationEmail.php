<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\BrevoTransactionalMailService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $orderId, public string $email)
    {
    }

    public function handle(BrevoTransactionalMailService $brevo): void
    {
        $order = Order::query()->with('items.product')->findOrFail($this->orderId);

        if ($order->confirmation_sent_at) {
            return;
        }

        $pdf = Pdf::loadView('pdf.proforma', ['order' => $order])->output();
        $customerName = data_get($order->customer_details, 'name');

        $brevo->send(
            to: [[
                'email' => $this->email,
                'name' => is_string($customerName) ? $customerName : null,
            ]],
            subject: 'Confirmare comandă '.$order->order_number.' - '.config('shop.brand_name'),
            htmlContent: view('emails.order_confirmation', ['order' => $order])->render(),
            attachments: [[
                'name' => 'Proforma_'.$order->proforma_number.'.pdf',
                'content' => base64_encode($pdf),
            ]],
            tags: ['order-confirmation'],
        );

        $order->update([
            'confirmation_sent_at' => now(),
            'confirmation_queued_at' => null,
            'confirmation_failed_at' => null,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Order::query()->whereKey($this->orderId)->update([
            'confirmation_queued_at' => null,
            'confirmation_failed_at' => now(),
        ]);
    }
}
