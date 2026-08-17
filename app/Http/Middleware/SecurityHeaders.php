<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if (! app()->environment('local')) {
            $scriptSources = [
                "'self'",
                "'unsafe-inline'",
                'https://js.stripe.com',
                'https://m.stripe.network',
            ];

            $connectSources = [
                "'self'",
                'https://api.stripe.com',
                'https://*.stripe.com',
                'https://m.stripe.network',
            ];

            $frameSources = [
                "'self'",
                'https://js.stripe.com',
                'https://hooks.stripe.com',
                'https://*.stripe.com',
            ];

            if (config('marketing.tracking_enabled', false)) {
                $scriptSources[] = 'https://www.googletagmanager.com';
                $connectSources[] = 'https://www.googletagmanager.com';
                $frameSources[] = 'https://www.googletagmanager.com';
            }

            if ($request->is('admin*') || $request->is('admin-security*')) {
                $scriptSources[] = "'unsafe-eval'";
            }

            $response->headers->set('Content-Security-Policy', implode('; ', [
                "default-src 'self'",
                "base-uri 'self'",
                "object-src 'none'",
                "frame-ancestors 'self'",
                "form-action 'self'",
                'script-src '.implode(' ', $scriptSources),
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                "font-src 'self' data: https://fonts.gstatic.com",
                "img-src 'self' data: blob: https: https://*.stripe.com",
                'connect-src '.implode(' ', $connectSources),
                'frame-src '.implode(' ', $frameSources),
                "worker-src 'self' blob:",
                "manifest-src 'self'",
                'upgrade-insecure-requests',
            ]));
        }

        if (app()->isProduction() && $request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains',
            );
        }

        return $response;
    }
}
