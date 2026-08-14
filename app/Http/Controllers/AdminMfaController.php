<?php

namespace App\Http\Controllers;

use App\Mail\AdminMfaCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AdminMfaController extends Controller
{
    public function show(Request $request): View
    {
        $this->ensureAdmin($request);
        $this->issueCodeIfNeeded($request);

        return view('auth.admin-mfa', [
            'email' => $this->maskedEmail((string) $request->user()->email),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $key = 'admin-mfa-verify:'.$request->user()->getKey().'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, (int) config('security.admin_mfa.verify_attempts', 5))) {
            return back()->withErrors(['code' => 'Prea multe încercări. Reîncearcă peste un minut.']);
        }
        RateLimiter::hit($key, 60);

        $hash = (string) $request->session()->get('admin_mfa_code_hash', '');
        $expiresAt = (int) $request->session()->get('admin_mfa_code_expires_at', 0);
        $userId = (int) $request->session()->get('admin_mfa_code_user_id', 0);

        if ($hash === '' || $expiresAt < time() || $userId !== (int) $request->user()->getKey() || ! Hash::check($validated['code'], $hash)) {
            return back()->withErrors(['code' => 'Cod invalid sau expirat.']);
        }

        RateLimiter::clear($key);
        $request->session()->forget(['admin_mfa_code_hash', 'admin_mfa_code_expires_at', 'admin_mfa_code_user_id']);
        $request->session()->put([
            'admin_mfa_verified_at' => time(),
            'admin_mfa_user_id' => (int) $request->user()->getKey(),
        ]);

        return redirect('/admin');
    }

    public function resend(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);
        $request->session()->forget(['admin_mfa_code_hash', 'admin_mfa_code_expires_at', 'admin_mfa_code_user_id']);

        if (! $this->issueCodeIfNeeded($request, force: true)) {
            return back()->withErrors([
                'code' => 'Codul nu a putut fi trimis momentan. Verifică serviciul de email și încearcă din nou.',
            ]);
        }

        return back()->with('status', 'A fost trimis un cod nou.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    private function issueCodeIfNeeded(Request $request, bool $force = false): bool
    {
        $expiresAt = (int) $request->session()->get('admin_mfa_code_expires_at', 0);
        $sameUser = (int) $request->session()->get('admin_mfa_code_user_id', 0) === (int) $request->user()->getKey();

        if (! $force && $sameUser && $expiresAt > time()) {
            return true;
        }

        $key = 'admin-mfa-send:'.$request->user()->getKey().'|'.$request->ip();
        $maxAttempts = (int) config('security.admin_mfa.send_attempts', 3);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $request->session()->flash('error', 'Ai solicitat prea multe coduri. Încearcă din nou în câteva minute.');

            return false;
        }

        $code = (string) random_int(100000, 999999);
        $codeSeconds = (int) config('security.admin_mfa.code_seconds', 600);
        $expiresInMinutes = max(1, (int) ceil($codeSeconds / 60));

        try {
            Mail::to((string) $request->user()->email)
                ->send(new AdminMfaCodeMail($code, $expiresInMinutes));
        } catch (\Throwable $exception) {
            Log::error('Admin MFA email could not be sent.', [
                'user_id' => $request->user()->getKey(),
                'exception' => $exception->getMessage(),
            ]);

            $request->session()->flash('error', 'Codul de securitate nu a putut fi trimis. Încearcă din nou.');

            return false;
        }

        RateLimiter::hit($key, $codeSeconds);
        $request->session()->put([
            'admin_mfa_code_hash' => Hash::make($code),
            'admin_mfa_code_expires_at' => time() + $codeSeconds,
            'admin_mfa_code_user_id' => (int) $request->user()->getKey(),
        ]);

        return true;
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin === true, 403);
    }

    private function maskedEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));

        return $visible.str_repeat('•', max(3, mb_strlen($local) - mb_strlen($visible))).'@'.$domain;
    }
}
