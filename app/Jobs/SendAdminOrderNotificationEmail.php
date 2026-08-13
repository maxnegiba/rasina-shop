<?php

namespace App\Jobs;

use App\Mail\AdminOrderPaidMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAdminOrderNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $orderId, public string $email)
    {
    }

    public function handle(): void
    {
        $order = Order::query()->with('items.product')->findOrFail($this->orderId);

        if ($order->admin_notification_sent_at) {
            return;
        }

        Mail::to($this->email)->send(new AdminOrderPaidMail($order));

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
