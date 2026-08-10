<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminMfa
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->is_admin !== true) {
            abort(403);
        }

        $verifiedAt = (int) $request->session()->get('admin_mfa_verified_at', 0);
        $verifiedUserId = (int) $request->session()->get('admin_mfa_user_id', 0);
        $maxAgeSeconds = (int) config('security.admin_mfa.session_seconds', 14400);

        $isVerified = $verifiedUserId === (int) $user->getKey()
            && $verifiedAt > 0
            && (time() - $verifiedAt) < $maxAgeSeconds;

        if (! $isVerified) {
            return redirect()->route('admin.mfa.challenge');
        }

        return $next($request);
    }
}
