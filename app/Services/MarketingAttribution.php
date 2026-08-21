<?php

namespace App\Services;

use Illuminate\Http\Request;

class MarketingAttribution
{
    public const COOKIE_NAME = 'mtd_marketing_attribution';

    /** @return array<string, mixed> */
    public function orderAttributes(Request $request): array
    {
        $payload = $this->decodeCookie($request->cookie(self::COOKIE_NAME));

        if ($payload === null) {
            return [];
        }

        $firstTouch = $this->sanitizeTouch(data_get($payload, 'first_touch'));
        $lastTouch = $this->sanitizeTouch(data_get($payload, 'last_touch'));

        if ($lastTouch === []) {
            return [];
        }

        return [
            'utm_source' => $lastTouch['utm_source'] ?? null,
            'utm_medium' => $lastTouch['utm_medium'] ?? null,
            'utm_campaign' => $lastTouch['utm_campaign'] ?? null,
            'utm_content' => $lastTouch['utm_content'] ?? null,
            'utm_term' => $lastTouch['utm_term'] ?? null,
            'marketing_attribution' => [
                'version' => 1,
                'first_touch' => $firstTouch ?: null,
                'last_touch' => $lastTouch,
            ],
        ];
    }

    private function decodeCookie(mixed $value): ?array
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded) && str_contains($value, '%')) {
            $decoded = json_decode(rawurldecode($value), true);
        }

        return is_array($decoded) ? $decoded : null;
    }

    /** @return array<string, string> */
    private function sanitizeTouch(mixed $touch): array
    {
        if (! is_array($touch)) {
            return [];
        }

        $limits = [
            'utm_source' => 120,
            'utm_medium' => 120,
            'utm_campaign' => 180,
            'utm_content' => 180,
            'utm_term' => 180,
            'landing_path' => 500,
            'referrer_host' => 255,
            'captured_at' => 40,
        ];

        $clean = [];

        foreach ($limits as $key => $limit) {
            $value = $touch[$key] ?? null;

            if (! is_string($value)) {
                continue;
            }

            $value = trim($value);

            if ($value === '') {
                continue;
            }

            $clean[$key] = mb_substr($value, 0, $limit);
        }

        return $clean;
    }
}
