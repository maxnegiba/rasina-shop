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
        $isLivewireRequest = $this->isLivewireRequest($request);

        $isBoundToCurrentAdmin = $verifiedAt > 0
            && $verifiedUserId === (int) $user->getKey();

        // A Livewire request can represent a save, delete, bulk action, modal action,
        // table filter, pagination request, etc. If this browser session has already
        // completed MFA for the currently authenticated administrator, never interrupt
        // an in-flight Livewire action merely because an MFA timer crossed its boundary.
        // Laravel's authenticated session is still required, and a fresh full-page admin
        // navigation will enforce the configured MFA time windows below.
        if ($isLivewireRequest && $isBoundToCurrentAdmin) {
            $this->touchActivity($request, $now, $lastActivityAt);

            return $next($request);
        }

        $idleSeconds = max(60, (int) config('security.admin_mfa.idle_seconds', 7200));
        $absoluteSeconds = max($idleSeconds, (int) config('security.admin_mfa.absolute_seconds', 43200));

        $isInsideIdleWindow = $lastActivityAt > 0 && ($now - $lastActivityAt) < $idleSeconds;
        $isInsideAbsoluteWindow = $verifiedAt > 0 && ($now - $verifiedAt) < $absoluteSeconds;

        if (! $isBoundToCurrentAdmin || ! $isInsideIdleWindow || ! $isInsideAbsoluteWindow) {
            return $this->requireMfa($request);
        }

        $this->touchActivity($request, $now, $lastActivityAt);

        return $next($request);
    }

    private function requireMfa(Request $request): Response
    {
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

    private function touchActivity(Request $request, int $now, int $lastActivityAt): void
    {
        if ($lastActivityAt <= 0 || ($now - $lastActivityAt) >= self::ACTIVITY_WRITE_INTERVAL_SECONDS) {
            $request->session()->put('admin_mfa_last_activity_at', $now);
        }
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
