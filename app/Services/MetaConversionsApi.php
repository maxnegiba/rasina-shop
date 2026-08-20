<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MetaConversionsApi
{
    public function sendPurchase(array $event): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        $payload = [
            'data' => [$event],
        ];

        $testEventCode = trim((string) config('marketing.meta.test_event_code'));

        if ($testEventCode !== '') {
            $payload['test_event_code'] = $testEventCode;
        }

        $response = $this->http()->post($this->endpoint(), $payload);

        if ($response->failed()) {
            throw new RuntimeException(sprintf(
                'Meta Conversions API request failed with HTTP %d.',
                $response->status(),
            ));
        }
    }

    private function http(): PendingRequest
    {
        return Http::asJson()
            ->acceptJson()
            ->withToken((string) config('marketing.meta.capi_access_token'))
            ->connectTimeout(3)
            ->timeout(8)
            ->retry(2, 250, throw: false);
    }

    private function endpoint(): string
    {
        $version = trim((string) config('marketing.meta.graph_api_version', 'v23.0'));
        $pixelId = trim((string) config('marketing.meta.pixel_id'));

        return sprintf('https://graph.facebook.com/%s/%s/events', $version, $pixelId);
    }

    private function isConfigured(): bool
    {
        return (bool) config('marketing.tracking_enabled', false)
            && filled(config('marketing.meta.pixel_id'))
            && filled(config('marketing.meta.capi_access_token'));
    }
}
