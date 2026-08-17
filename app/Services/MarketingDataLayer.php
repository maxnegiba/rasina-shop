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

        $item = [
            'item_id' => filled($product->product_code)
                ? (string) $product->product_code
                : (string) $product->getKey(),
            'item_name' => (string) $product->name,
            'quantity' => 1,
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
            $ecommerce['value'] = $price;
            $ecommerce['items'][0]['price'] = $price;
        }

        $this->push('view_product', [
            'ecommerce' => $ecommerce,
        ]);
    }

    private function isEnabled(): bool
    {
        return (bool) config('marketing.tracking_enabled', false);
    }
}
