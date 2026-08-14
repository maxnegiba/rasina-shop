<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TraceCsrfToken extends ValidateCsrfToken
{
    protected function tokensMatch($request): bool
    {
        $matches = parent::tokensMatch($request);

        if ($request instanceof Request && $request->is('livewire/*')) {
            $session = $request->hasSession() ? $request->session() : null;
            $requestToken = $this->getTokenFromRequest($request);
            $sessionToken = $session?->token();

            Log::warning('livewire_csrf_trace', [
                'matches' => $matches,
                'path' => '/'.$request->path(),
                'method' => $request->method(),
                'user_id' => $request->user()?->getAuthIdentifier(),
                'session_id_fp' => $this->fingerprint($session?->getId()),
                'session_token_fp' => $this->fingerprint($sessionToken),
                'request_token_fp' => $this->fingerprint($requestToken),
                'input_token_fp' => $this->fingerprint($request->input('_token')),
                'x_csrf_token_fp' => $this->fingerprint($request->header('X-CSRF-TOKEN')),
                'x_xsrf_token_fp' => $this->fingerprint($request->header('X-XSRF-TOKEN')),
                'has_session_cookie' => $request->cookies->has((string) config('session.cookie')),
                'content_length' => $request->headers->getInt('Content-Length'),
                'content_type' => $request->header('Content-Type'),
            ]);
        }

        return $matches;
    }

    private function fingerprint(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        return substr(hash('sha256', $value), 0, 12);
    }
}
