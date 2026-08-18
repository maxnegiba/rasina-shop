<?php

namespace App\Http\Middleware;

use App\Models\Order;
use App\Services\MarketingDataLayer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPurchase
{
    public function __construct(
        private readonly MarketingDataLayer $dataLayer,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        $contentType = strtolower((string) $response->headers->get('Content-Type'));

        if (! str_contains($contentType, 'text/html')) {
            return $response;
        }

        $order = $this->resolvedSuccessfulOrder($request);

        if (! $order) {
            return $response;
        }

        $event = $this->dataLayer->purchaseEvent($order);

        if ($event === null) {
            return $response;
        }

        $transactionId = (string) data_get($event, 'ecommerce.transaction_id', '');

        if ($transactionId === '') {
            return $response;
        }

        $tracked = collect($request->session()->get('marketing_purchase_transaction_ids', []))
            ->filter(fn ($value): bool => is_string($value) && $value !== '')
            ->values();

        if ($tracked->contains($transactionId)) {
            return $response;
        }

        $this->dataLayer->push('purchase', [
            'ecommerce' => $event['ecommerce'],
        ]);

        $request->session()->put(
            'marketing_purchase_transaction_ids',
            $tracked->push($transactionId)->take(-20)->values()->all(),
        );

        return $response;
    }

    private function resolvedSuccessfulOrder(Request $request): ?Order
    {
        $orderToken = $request->string('order')->toString();

        if ($orderToken === '') {
            return null;
        }

        $order = Order::query()
            ->with('items.product.category')
            ->where('public_token', $orderToken)
            ->whereNull('cancelled_at')
            ->first();

        if (! $order) {
            return null;
        }

        if ($order->isCashOnDelivery()) {
            return $order;
        }

        return $order->payment_status === 'paid'
            ? $order
            : null;
    }
}
