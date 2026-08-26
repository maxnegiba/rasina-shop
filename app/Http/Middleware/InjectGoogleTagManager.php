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

        $encodedContainerId = $this->encodeForJavaScript($containerId);
        $cookieKey = $this->encodeForJavaScript((string) config('cookie-consent.cookie_key', '__cookie_consent'));
        $analyticsValue = $this->encodeForJavaScript((string) config('cookie-consent.cookie_value_analytics', '2'));
        $marketingValue = $this->encodeForJavaScript((string) config('cookie-consent.cookie_value_marketing', '3'));
        $bothValue = $this->encodeForJavaScript((string) config('cookie-consent.cookie_value_both', 'true'));
        $serverDataLayer = $this->renderServerDataLayer();

        $headSnippet = <<<HTML
<!-- Google Consent Mode + consent-aware GTM loader -->
<script>
window.dataLayer = window.dataLayer || [];
window.gtag = window.gtag || function(){window.dataLayer.push(arguments);};
(function(){
    var containerId = {$encodedContainerId};
    var cookieKey = {$cookieKey};
    var values = {
        analytics: {$analyticsValue},
        marketing: {$marketingValue},
        both: {$bothValue}
    };
    var loaded = false;
    var scheduled = false;

    function readConsent(){
        var prefix = encodeURIComponent(cookieKey) + '=';
        var parts = document.cookie ? document.cookie.split(';') : [];

        for (var i = 0; i < parts.length; i++) {
            var item = parts[i].trim();
            if (item.indexOf(prefix) === 0) {
                return decodeURIComponent(item.slice(prefix.length));
            }
        }

        return null;
    }

    function consentState(value){
        var analyticsGranted = value === values.analytics || value === values.both;
        var marketingGranted = value === values.marketing || value === values.both;

        return {
            analytics_storage: analyticsGranted ? 'granted' : 'denied',
            ad_storage: marketingGranted ? 'granted' : 'denied',
            ad_user_data: marketingGranted ? 'granted' : 'denied',
            ad_personalization: marketingGranted ? 'granted' : 'denied'
        };
    }

    function hasOptionalConsent(value){
        return value === values.analytics || value === values.marketing || value === values.both;
    }

    var current = readConsent();
    window.gtag('consent', 'default', consentState(current));
{$serverDataLayer}

    window.mtdLoadGtm = function(){
        if (loaded || !hasOptionalConsent(readConsent())) {
            return;
        }

        loaded = true;
        window.dataLayer.push({'gtm.start': new Date().getTime(), event: 'gtm.js'});

        var firstScript = document.getElementsByTagName('script')[0];
        var script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(containerId);
        firstScript.parentNode.insertBefore(script, firstScript);
    };

    function scheduleDeferredLoad(){
        if (scheduled || !hasOptionalConsent(readConsent())) {
            return;
        }

        scheduled = true;
        window.setTimeout(function(){
            window.mtdLoadGtm();
        }, 5000);
    }

    if (hasOptionalConsent(current)) {
        var loadOnInteraction = function(){
            window.mtdLoadGtm();
        };

        ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach(function(eventName){
            window.addEventListener(eventName, loadOnInteraction, {once: true, passive: true});
        });

        if (document.readyState === 'complete') {
            scheduleDeferredLoad();
        } else {
            window.addEventListener('load', scheduleDeferredLoad, {once: true});
        }
    }
})();
</script>
<!-- End Google Consent Mode + consent-aware GTM loader -->
HTML;

        $content = preg_replace('/<head(\s[^>]*)?>/i', '$0'."\n".$headSnippet, $content, 1) ?? $content;
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
