<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TraceLivewireSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('livewire/*')) {
            return $next($request);
        }

        try {
            $response = $next($request);

            $this->logSnapshot($request, $response->getStatusCode(), 'completed');

            return $response;
        } catch (TokenMismatchException $exception) {
            $this->logSnapshot($request, 419, 'token_mismatch');

            throw $exception;
        }
    }

    private function logSnapshot(Request $request, int $status, string $outcome): void
    {
        $session = $request->hasSession() ? $request->session() : null;

        Log::warning('livewire_session_trace', [
            'outcome' => $outcome,
            'status' => $status,
            'path' => '/'.$request->path(),
            'method' => $request->method(),
            'user_id' => $request->user()?->getAuthIdentifier(),
            'session_id_fp' => $this->fingerprint($session?->getId()),
            'session_token_fp' => $this->fingerprint($session?->token()),
            'input_token_fp' => $this->fingerprint($request->input('_token')),
            'x_csrf_token_fp' => $this->fingerprint($request->header('X-CSRF-TOKEN')),
            'x_xsrf_token_fp' => $this->fingerprint($request->header('X-XSRF-TOKEN')),
            'has_session_cookie' => $request->cookies->has((string) config('session.cookie')),
            'content_length' => $request->headers->getInt('Content-Length'),
            'content_type' => $request->header('Content-Type'),
        ]);
    }

    private function fingerprint(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        return substr(hash('sha256', $value), 0, 12);
    }
}
