<?php

namespace App\Http\Middleware;

use App\Services\MarketingAttribution;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CaptureMarketingAttribution
{
    private const TTL_MINUTES = 60 * 24 * 90;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->hasMarketingConsent($request)) {
            return $response;
        }

        $touch = $this->currentTouch($request);

        if ($touch === null) {
            return $response;
        }

        $existing = $this->decode($request->cookie(MarketingAttribution::COOKIE_NAME));
        $payload = [
            'version' => 1,
            'first_touch' => data_get($existing, 'first_touch') ?: $touch,
            'last_touch' => $touch,
        ];

        $response->headers->setCookie(new Cookie(
            name: MarketingAttribution::COOKIE_NAME,
            value: json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            expire: now()->addMinutes(self::TTL_MINUTES),
            path: '/',
            domain: config('session.domain'),
            secure: (bool) config('session.secure'),
            httpOnly: false,
            raw: false,
            sameSite: 'lax',
        ));

        return $response;
    }

    private function hasMarketingConsent(Request $request): bool
    {
        return in_array((string) $request->cookie('__cookie_consent', 'false'), ['3', 'true'], true);
    }

    /** @return array<string, string>|null */
    private function currentTouch(Request $request): ?array
    {
        $utm = [];

        foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'] as $key) {
            $value = trim($request->string($key)->toString());

            if ($value !== '') {
                $utm[$key] = mb_substr($value, 0, $key === 'utm_source' || $key === 'utm_medium' ? 120 : 180);
            }
        }

        if ($utm === []) {
            return null;
        }

        $touch = $utm;
        $touch['landing_path'] = mb_substr('/'.ltrim($request->path(), '/'), 0, 500);

        $referrer = (string) $request->headers->get('referer', '');
        $host = $referrer !== '' ? parse_url($referrer, PHP_URL_HOST) : null;

        if (is_string($host) && $host !== '') {
            $touch['referrer_host'] = mb_substr(strtolower($host), 0, 255);
        }

        $touch['captured_at'] = now()->toIso8601String();

        return $touch;
    }

    private function decode(mixed $value): ?array
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : null;
    }
}
