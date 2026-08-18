<?php

namespace App\Services;

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
