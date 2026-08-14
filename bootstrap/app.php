<?php

use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TraceCsrfToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);

        // Temporary diagnostic replacement at Laravel's actual CSRF decision point.
        // It preserves normal validation and logs only short SHA-256 fingerprints.
        $middleware->web(replace: [
            ValidateCsrfToken::class => TraceCsrfToken::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhook/stripe',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
