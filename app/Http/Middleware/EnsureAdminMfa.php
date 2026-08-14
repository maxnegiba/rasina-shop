<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminMfa
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->is_admin !== true) {
            abort(403);
        }

        $verifiedAt = (int) $request->session()->get('admin_mfa_verified_at', 0);
        $verifiedUserId = (int) $request->session()->get('admin_mfa_user_id', 0);

        $isVerifiedForCurrentSession = $verifiedAt > 0
            && $verifiedUserId === (int) $user->getKey();

        if (! $isVerifiedForCurrentSession) {
            return $this->requireMfa($request);
        }

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
