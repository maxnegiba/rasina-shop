<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\BrevoTransactionalMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAdminOrderNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $orderId, public string $email)
    {
    }

    public function handle(BrevoTransactionalMailService $brevo): void
    {
        $order = Order::query()->with('items.product')->findOrFail($this->orderId);

        if ($order->admin_notification_sent_at) {
            return;
        }

        $customerEmail = filter_var(data_get($order->customer_details, 'email'), FILTER_VALIDATE_EMAIL) ?: null;
        $customerName = data_get($order->customer_details, 'name');

        $brevo->send(
            to: [$this->email],
            subject: 'Comandă plătită '.$order->order_number.' - '.config('shop.brand_name'),
            htmlContent: view('emails.admin_order_paid', ['order' => $order])->render(),
            replyTo: $customerEmail ? [
                'email' => $customerEmail,
                'name' => is_string($customerName) ? $customerName : null,
            ] : null,
            tags: ['paid-order-admin'],
        );

        $order->update([
            'admin_notification_sent_at' => now(),
            'admin_notification_queued_at' => null,
            'admin_notification_failed_at' => null,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Order::query()->whereKey($this->orderId)->update([
            'admin_notification_queued_at' => null,
            'admin_notification_failed_at' => now(),
        ]);
    }
}
