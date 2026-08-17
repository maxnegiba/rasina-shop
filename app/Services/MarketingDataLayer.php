<?php

namespace App\Services;

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

    private function isEnabled(): bool
    {
        return (bool) config('marketing.tracking_enabled', false);
    }
}
