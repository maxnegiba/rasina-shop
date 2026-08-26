<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\MetaConversionsApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class SendMetaPurchase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 60, 300];

    public function __construct(
        public readonly int $orderId,
        public readonly string $eventId,
        public readonly int $eventTime,
        public readonly string $eventSourceUrl,
        public readonly array $userData = [],
    ) {}

    public function handle(MetaConversionsApi $meta): void
    {
        $order = Order::query()
            ->with('items.product.category')
            ->find($this->orderId);

        if (! $order || $order->isCancelled()) {
            return;
        }

        if (! $order->isCashOnDelivery() && $order->payment_status !== 'paid') {
            return;
        }

        $items = $order->items->map(function ($item): array {
            return [
                'id' => filled($item->product_code)
                    ? (string) $item->product_code
                    : (string) ($item->product_id ?: $item->getKey()),
                'quantity' => max(1, (int) $item->quantity),
                'item_price' => round((float) $item->unit_price, 2),
            ];
        })->values()->all();

        if ($items === []) {
            return;
        }

        $shipping = round((float) ($order->shipping_amount ?? 0), 2);
        $total = round((float) $order->total_amount, 2);
        $value = round(max(0, $total - $shipping), 2);

        $meta->sendPurchase([
            'event_name' => 'Purchase',
            'event_time' => $this->eventTime,
            'event_id' => $this->eventId,
            'action_source' => 'website',
            'event_source_url' => $this->eventSourceUrl,
            'user_data' => Arr::where($this->userData, fn ($value): bool => filled($value)),
            'custom_data' => [
                'currency' => 'RON',
                'value' => $value,
                'order_id' => (string) $order->order_number,
                'contents' => $items,
                'content_type' => 'product',
            ],
        ]);
    }
}
