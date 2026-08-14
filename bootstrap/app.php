<?php

use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TraceLivewireSession;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Temporary diagnostic wrapper around Livewire requests. It records only
        // short SHA-256 fingerprints, never raw session IDs or CSRF tokens.
        $middleware->append(TraceLivewireSession::class);
        $middleware->append(SecurityHeaders::class);

        $middleware->validateCsrfTokens(except: [
            'webhook/stripe',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
