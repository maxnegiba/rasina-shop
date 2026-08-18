<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Spatie\GoogleTagManager\GoogleTagManager;

class MarketingDataLayer
{
    public function __construct(
        private readonly GoogleTagManager $googleTagManager,
    ) {}

    public function push(string $event, array $payload = []): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $this->googleTagManager->push(array_merge(
            ['event' => $event],
            $payload,
        ));
    }

    public function flashPush(string $event, array $payload = []): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $this->googleTagManager->flashPush(array_merge(
            ['event' => $event],
            $payload,
        ));
    }

    public function viewProduct(Product $product): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $this->push('view_product', $this->productEcommercePayload($product, 1));
    }

    public function addToCartEvent(Product $product, int $quantity): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        return array_merge(
            ['event' => 'add_to_cart'],
            $this->productEcommercePayload($product, max(1, $quantity)),
        );
    }

    public function flashAddToCart(Product $product, int $quantity): void
    {
        $event = $this->addToCartEvent($product, $quantity);

        if ($event === null) {
            return;
        }

        $this->googleTagManager->flashPush($event);
    }

    public function beginCheckoutEvent(array $cart): ?array
    {
        if (! $this->isEnabled() || $cart === []) {
            return null;
        }

        $productIds = collect($cart)
            ->pluck('id')
            ->filter(fn ($id): bool => is_numeric($id))
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();

        $products = Product::query()
            ->with('category')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $items = [];
        $value = 0.0;

        foreach ($cart as $cartItem) {
            if (! is_array($cartItem)) {
                continue;
            }

            $productId = (int) ($cartItem['id'] ?? 0);
            $quantity = max(1, (int) ($cartItem['quantity'] ?? 1));
            $price = round((float) ($cartItem['price'] ?? 0), 2);
            /** @var Product|null $product */
            $product = $products->get($productId);

            $item = [
                'item_id' => $product && filled($product->product_code)
                    ? (string) $product->product_code
                    : (string) $productId,
                'item_name' => $product
                    ? (string) $product->name
                    : (string) ($cartItem['name'] ?? 'Produs'),
                'price' => $price,
                'quantity' => $quantity,
            ];

            if ($product?->category) {
                $item['item_category'] = (string) $product->category->name;
            }

            if ($product && filled($product->product_type)) {
                $item['item_variant'] = (string) $product->product_type;
            }

            $items[] = $item;
            $value += $price * $quantity;
        }

        if ($items === []) {
            return null;
        }

        return [
            'event' => 'begin_checkout',
            'ecommerce' => [
                'currency' => 'RON',
                'value' => round($value, 2),
                'items' => $items,
            ],
        ];
    }

    public function pushBeginCheckout(array $cart): void
    {
        $event = $this->beginCheckoutEvent($cart);

        if ($event === null) {
            return;
        }

        $this->googleTagManager->push($event);
    }

    public function purchaseEvent(Order $order): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $order->loadMissing('items.product.category');

        if ($order->items->isEmpty()) {
            return null;
        }

        $items = [];

        foreach ($order->items as $orderItem) {
            $product = $orderItem->product;
            $quantity = max(1, (int) $orderItem->quantity);
            $price = round((float) $orderItem->unit_price, 2);

            $item = [
                'item_id' => filled($orderItem->product_code)
                    ? (string) $orderItem->product_code
                    : ($product ? (string) $product->getKey() : (string) $orderItem->getKey()),
                'item_name' => (string) ($orderItem->product_name ?: $product?->name ?: 'Produs'),
                'price' => $price,
                'quantity' => $quantity,
            ];

            if ($product?->category) {
                $item['item_category'] = (string) $product->category->name;
            }

            if ($product && filled($product->product_type)) {
                $item['item_variant'] = (string) $product->product_type;
            }

            $items[] = $item;
        }

        $shipping = round((float) ($order->shipping_amount ?? 0), 2);
        $total = round((float) $order->total_amount, 2);
        $value = round(max(0, $total - $shipping), 2);

        return [
            'event' => 'purchase',
            'ecommerce' => [
                'transaction_id' => (string) $order->order_number,
                'currency' => 'RON',
                'value' => $value,
                'shipping' => $shipping,
                'items' => $items,
            ],
        ];
    }

    private function productEcommercePayload(Product $product, int $quantity): array
    {
        $quantity = max(1, $quantity);

        $item = [
            'item_id' => filled($product->product_code)
                ? (string) $product->product_code
                : (string) $product->getKey(),
            'item_name' => (string) $product->name,
            'quantity' => $quantity,
        ];

        if ($product->relationLoaded('category') && $product->category) {
            $item['item_category'] = (string) $product->category->name;
        }

        if (filled($product->product_type)) {
            $item['item_variant'] = (string) $product->product_type;
        }

        $ecommerce = [
            'currency' => 'RON',
            'items' => [$item],
        ];

        if ($product->price !== null && (float) $product->price > 0) {
            $price = round((float) $product->price, 2);
            $ecommerce['value'] = round($price * $quantity, 2);
            $ecommerce['items'][0]['price'] = $price;
        }

        return ['ecommerce' => $ecommerce];
    }

    private function isEnabled(): bool
    {
        return (bool) config('marketing.tracking_enabled', false);
    }
}
