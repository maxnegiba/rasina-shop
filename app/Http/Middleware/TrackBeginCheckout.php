<?php

namespace App\Http\Middleware;

use App\Services\MarketingDataLayer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackBeginCheckout
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

        $cart = $request->session()->get('cart', []);

        if (! is_array($cart) || $cart === []) {
            return $response;
        }

        $event = $this->dataLayer->beginCheckoutEvent($cart);

        if ($event === null) {
            return $response;
        }

        $fingerprint = $this->fingerprint($request, $cart);

        if (hash_equals(
            (string) $request->session()->get('marketing_begin_checkout_fingerprint', ''),
            $fingerprint,
        )) {
            return $response;
        }

        $this->dataLayer->push('begin_checkout', [
            'ecommerce' => $event['ecommerce'],
        ]);

        $request->session()->put('marketing_begin_checkout_fingerprint', $fingerprint);

        return $response;
    }

    private function fingerprint(Request $request, array $cart): string
    {
        $normalizedCart = collect($cart)
            ->map(fn ($item): array => [
                'id' => (int) data_get($item, 'id', 0),
                'quantity' => (int) data_get($item, 'quantity', 0),
                'price' => (string) data_get($item, 'price', ''),
            ])
            ->sortBy('id')
            ->values()
            ->all();

        return hash('sha256', json_encode([
            'checkout_order_token' => (string) $request->session()->get('checkout_order_token', ''),
            'cart' => $normalizedCart,
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }
}
