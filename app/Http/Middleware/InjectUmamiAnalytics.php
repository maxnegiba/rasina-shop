<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectUmamiAnalytics
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldInject($request, $response)) {
            return $response;
        }

        $websiteId = trim((string) config('marketing.umami.website_id'));
        $scriptUrl = trim((string) config('marketing.umami.script_url'));
        $domains = trim((string) config('marketing.umami.domains'));

        if (! $this->hasValidConfiguration($websiteId, $scriptUrl, $domains)) {
            return $response;
        }

        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return $response;
        }

        $snippet = $this->renderLoader($websiteId, $scriptUrl, $domains);
        $content = preg_replace('/<head(\s[^>]*)?>/i', '$0'."\n".$snippet, $content, 1) ?? $content;
        $response->setContent($content);

        return $response;
    }

    private function shouldInject(Request $request, Response $response): bool
    {
        if (! config('marketing.umami.enabled', false)) {
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

    private function hasValidConfiguration(string $websiteId, string $scriptUrl, string $domains): bool
    {
        if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $websiteId)) {
            return false;
        }

        $parts = parse_url($scriptUrl);
        if (
            ! is_array($parts)
            || ($parts['scheme'] ?? null) !== 'https'
            || ($parts['host'] ?? null) !== 'analytics.mtdart.ro'
            || ($parts['path'] ?? null) !== '/script.js'
        ) {
            return false;
        }

        foreach (array_filter(array_map('trim', explode(',', $domains))) as $domain) {
            if (! preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', $domain)) {
                return false;
            }
        }

        return $domains !== '';
    }

    private function renderLoader(string $websiteId, string $scriptUrl, string $domains): string
    {
        $encodedWebsiteId = $this->encodeForJavaScript($websiteId);
        $encodedScriptUrl = $this->encodeForJavaScript($scriptUrl);
        $encodedDomains = $this->encodeForJavaScript($domains);
        $cookieKey = $this->encodeForJavaScript((string) config('cookie-consent.cookie_key', '__cookie_consent'));
        $analyticsValue = $this->encodeForJavaScript((string) config('cookie-consent.cookie_value_analytics', '2'));
        $bothValue = $this->encodeForJavaScript((string) config('cookie-consent.cookie_value_both', 'true'));

        return <<<HTML
<!-- Consent-aware Umami analytics -->
<script>
(function(){
    var scriptUrl = {$encodedScriptUrl};
    var websiteId = {$encodedWebsiteId};
    var domains = {$encodedDomains};
    var cookieKey = {$cookieKey};
    var analyticsValue = {$analyticsValue};
    var bothValue = {$bothValue};
    var loaded = false;

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

    function analyticsGranted(){
        var value = readConsent();
        return value === analyticsValue || value === bothValue;
    }

    function loadUmami(){
        if (loaded || !analyticsGranted()) {
            return;
        }

        if (document.querySelector('script[data-mtd-umami="true"]')) {
            loaded = true;
            return;
        }

        loaded = true;
        var script = document.createElement('script');
        script.defer = true;
        script.src = scriptUrl;
        script.dataset.websiteId = websiteId;
        script.dataset.domains = domains;
        script.dataset.mtdUmami = 'true';
        document.head.appendChild(script);
    }

    if (analyticsGranted()) {
        loadUmami();
    }

    document.addEventListener('click', function(event){
        var target = event.target instanceof Element ? event.target : null;
        if (!target || !target.closest('.js-lcc-accept, .js-lcc-settings-save')) {
            return;
        }

        window.setTimeout(loadUmami, 0);
    });
})();
</script>
<!-- End consent-aware Umami analytics -->
HTML;
    }

    private function encodeForJavaScript(string $value): string
    {
        return (string) json_encode(
            $value,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES,
        );
    }
}
