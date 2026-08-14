<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminMfa
{
    private const ACTIVITY_WRITE_INTERVAL_SECONDS = 60;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->is_admin !== true) {
            abort(403);
        }

        $now = time();
        $verifiedAt = (int) $request->session()->get('admin_mfa_verified_at', 0);
        $verifiedUserId = (int) $request->session()->get('admin_mfa_user_id', 0);
        $lastActivityAt = (int) $request->session()->get('admin_mfa_last_activity_at', $verifiedAt);
        $idleSeconds = max(60, (int) config('security.admin_mfa.idle_seconds', 7200));
        $absoluteSeconds = max($idleSeconds, (int) config('security.admin_mfa.absolute_seconds', 43200));

        $isBoundToCurrentAdmin = $verifiedUserId === (int) $user->getKey();
        $isInsideIdleWindow = $lastActivityAt > 0 && ($now - $lastActivityAt) < $idleSeconds;
        $isInsideAbsoluteWindow = $verifiedAt > 0 && ($now - $verifiedAt) < $absoluteSeconds;

        if (! $isBoundToCurrentAdmin || ! $isInsideIdleWindow || ! $isInsideAbsoluteWindow) {
            $this->rememberIntendedAdminUrl($request);
            $request->session()->forget([
                'admin_mfa_verified_at',
                'admin_mfa_last_activity_at',
                'admin_mfa_user_id',
            ]);

            if ($this->isLivewireRequest($request)) {
                return response()->json([
                    'message' => 'Verificarea de securitate a sesiunii de administrator este necesară.',
                    'code' => 'ADMIN_MFA_REQUIRED',
                    'redirect' => route('admin.mfa.challenge'),
                ], 401);
            }

            return redirect()->route('admin.mfa.challenge');
        }

        if (($now - $lastActivityAt) >= self::ACTIVITY_WRITE_INTERVAL_SECONDS) {
            $request->session()->put('admin_mfa_last_activity_at', $now);
        }

        return $next($request);
    }

    private function isLivewireRequest(Request $request): bool
    {
        return $request->headers->has('X-Livewire') || $request->is('livewire/*');
    }

    private function rememberIntendedAdminUrl(Request $request): void
    {
        $candidate = $request->isMethod('GET')
            ? $request->fullUrl()
            : (string) $request->headers->get('referer', '');

        if ($candidate === '') {
            return;
        }

        $parts = parse_url($candidate);

        if (! is_array($parts)) {
            return;
        }

        $host = $parts['host'] ?? null;
        $path = $parts['path'] ?? '/';

        if ($host !== $request->getHost() || ! str_starts_with($path, '/admin')) {
            return;
        }

        $request->session()->put('admin_mfa_intended_url', $candidate);
    }
}
