<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $orderId, public string $email)
    {
    }

    public function handle(): void
    {
        $order = Order::query()->with('items.product')->findOrFail($this->orderId);

        if ($order->confirmation_sent_at) {
            return;
        }

        Mail::to($this->email)->send(new OrderConfirmationMail($order));

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
