<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectGoogleTagManager
{
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

        $headSnippet = <<<HTML
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
}
