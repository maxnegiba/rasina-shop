<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\GoogleTagManager\GoogleTagManager;
use Symfony\Component\HttpFoundation\Response;

class InjectGoogleTagManager
{
    public function __construct(
        private readonly GoogleTagManager $googleTagManager,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldInject($request, $response)) {
            return $response;
        }

        $containerId = trim((string) config('marketing.gtm.container_id'));

        if (! preg_match('/^GTM-[A-Z0-9]+$/i', $containerId)) {
            return $response;
        }

        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return $response;
        }

        $escapedContainerId = htmlspecialchars($containerId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $cookieKey = $this->encodeForJavaScript((string) config('cookie-consent.cookie_key', '__cookie_consent'));
        $analyticsValue = $this->encodeForJavaScript((string) config('cookie-consent.cookie_value_analytics', '2'));
        $marketingValue = $this->encodeForJavaScript((string) config('cookie-consent.cookie_value_marketing', '3'));
        $bothValue = $this->encodeForJavaScript((string) config('cookie-consent.cookie_value_both', 'true'));
        $serverDataLayer = $this->renderServerDataLayer();

        $headSnippet = <<<HTML
<!-- Google Consent Mode defaults -->
<script>
window.dataLayer = window.dataLayer || [];
window.gtag = window.gtag || function(){window.dataLayer.push(arguments);};
(function(){
    var cookieKey = {$cookieKey};
    var values = {
        analytics: {$analyticsValue},
        marketing: {$marketingValue},
        both: {$bothValue}
    };
    var prefix = encodeURIComponent(cookieKey) + '=';
    var cookieEntry = document.cookie.split(';').map(function(item){ return item.trim(); }).find(function(item){ return item.indexOf(prefix) === 0; });
    var current = cookieEntry ? decodeURIComponent(cookieEntry.slice(prefix.length)) : null;
    var analyticsGranted = current === values.analytics || current === values.both;
    var marketingGranted = current === values.marketing || current === values.both;

    window.gtag('consent', 'default', {
        analytics_storage: analyticsGranted ? 'granted' : 'denied',
        ad_storage: marketingGranted ? 'granted' : 'denied',
        ad_user_data: marketingGranted ? 'granted' : 'denied',
        ad_personalization: marketingGranted ? 'granted' : 'denied'
    });
})();
{$serverDataLayer}
</script>
<!-- End Google Consent Mode defaults -->
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$escapedContainerId}');</script>
<!-- End Google Tag Manager -->
HTML;

        $bodySnippet = <<<HTML
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$escapedContainerId}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
HTML;

        $content = preg_replace('/<head(\s[^>]*)?>/i', '$0'."\n".$headSnippet, $content, 1) ?? $content;
        $content = preg_replace('/<body(\s[^>]*)?>/i', '$0'."\n".$bodySnippet, $content, 1) ?? $content;

        $response->setContent($content);

        return $response;
    }

    private function shouldInject(Request $request, Response $response): bool
    {
        if (! config('marketing.tracking_enabled', false)) {
            return false;
        }

        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->is('admin*') || $request->is('admin-security*') || $request->is('livewire*')) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        return $response->isSuccessful()
            && ($contentType === '' || str_contains(strtolower($contentType), 'text/html'));
    }

    private function renderServerDataLayer(): string
    {
        $lines = [];
        $baseData = $this->googleTagManager->getDataLayer()->toArray();

        if ($baseData !== [] && ($json = $this->encodeDataLayerPayload($baseData)) !== null) {
            $lines[] = "window.dataLayer.push({$json});";
        }

        foreach ($this->googleTagManager->getPushData() as $pushData) {
            $payload = $pushData->toArray();

            if ($payload !== [] && ($json = $this->encodeDataLayerPayload($payload)) !== null) {
                $lines[] = "window.dataLayer.push({$json});";
            }
        }

        return implode("\n", $lines);
    }

    private function encodeDataLayerPayload(array $payload): ?string
    {
        try {
            return json_encode(
                $payload,
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException) {
            // Analytics must never be able to break a storefront response.
            return null;
        }
    }

    private function encodeForJavaScript(string $value): string
    {
        return (string) json_encode(
            $value,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES,
        );
    }
}
