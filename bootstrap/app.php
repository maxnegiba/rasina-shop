<?php

use App\Http\Middleware\InjectGoogleTagManager;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\GoogleTagManager\GoogleTagManagerMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);

        // The cookie-consent package writes a plain browser-readable cookie so the
        // frontend can update Consent Mode immediately. Laravel must not try to
        // decrypt it, otherwise server-side marketing consent checks see it as null.
        $middleware->encryptCookies(except: [
            '__cookie_consent',
        ]);

        $middleware->web(append: [
            \Statikbe\CookieConsent\CookieConsentMiddleware::class,
            GoogleTagManagerMiddleware::class,
            InjectGoogleTagManager::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhook/stripe',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
