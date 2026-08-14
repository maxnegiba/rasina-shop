<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class TraceCsrfToken extends ValidateCsrfToken
{
    public function handle($request, Closure $next)
    {
        if (! ($request instanceof Request) || ! $request->is('livewire/*')) {
            return parent::handle($request, $next);
        }

        try {
            $response = parent::handle($request, $next);

            if (method_exists($response, 'getStatusCode') && $response->getStatusCode() === 419) {
                Log::warning('livewire_419_origin', [
                    'kind' => 'response',
                    'status' => 419,
                    'path' => '/'.$request->path(),
                    'user_id' => $request->user()?->getAuthIdentifier(),
                    'component_names' => $this->componentNames($request),
                ]);
            }

            return $response;
        } catch (Throwable $exception) {
            $status = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : null;

            if ($status === 419) {
                Log::warning('livewire_419_origin', [
                    'kind' => 'exception',
                    'status' => 419,
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'path' => '/'.$request->path(),
                    'user_id' => $request->user()?->getAuthIdentifier(),
                    'component_names' => $this->componentNames($request),
                ]);
            }

            throw $exception;
        }
    }

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
                'content_length' => (int) ($request->header('Content-Length') ?? 0),
                'content_type' => $request->header('Content-Type'),
            ]);
        }

        return $matches;
    }

    private function componentNames(Request $request): array
    {
        $components = $request->input('components', []);

        if (! is_array($components)) {
            return [];
        }

        $names = [];

        foreach ($components as $component) {
            if (! is_array($component)) {
                continue;
            }

            $snapshot = $component['snapshot'] ?? null;

            if (! is_string($snapshot)) {
                continue;
            }

            $decoded = json_decode($snapshot, true);
            $name = is_array($decoded) ? ($decoded['memo']['name'] ?? null) : null;

            if (is_string($name) && $name !== '') {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    private function fingerprint(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        return substr(hash('sha256', $value), 0, 12);
    }
}
