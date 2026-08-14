<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureAdminMfa;
use App\Mail\AdminMfaCodeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminMfaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_without_recent_mfa_is_redirected_to_challenge(): void
    {
        Route::middleware(EnsureAdminMfa::class)
            ->get('/_admin-mfa-test', fn () => response('ok'));

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/_admin-mfa-test')
            ->assertRedirect(route('admin.mfa.challenge'));
    }

    public function test_admin_with_recent_mfa_can_continue(): void
    {
        Route::middleware(EnsureAdminMfa::class)
            ->get('/_admin-mfa-verified-test', fn () => response('ok'));

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession([
                'admin_mfa_verified_at' => time(),
                'admin_mfa_last_activity_at' => time(),
                'admin_mfa_user_id' => $admin->id,
            ])
            ->get('/_admin-mfa-verified-test')
            ->assertOk()
            ->assertSee('ok');
    }

    public function test_active_admin_can_continue_past_the_old_four_hour_cutoff(): void
    {
        Route::middleware(EnsureAdminMfa::class)
            ->get('/_admin-mfa-active-test', fn () => response('ok'));

        config([
            'security.admin_mfa.idle_seconds' => 7200,
            'security.admin_mfa.absolute_seconds' => 43200,
        ]);

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession([
                'admin_mfa_verified_at' => time() - 18000,
                'admin_mfa_last_activity_at' => time() - 30,
                'admin_mfa_user_id' => $admin->id,
            ])
            ->get('/_admin-mfa-active-test')
            ->assertOk()
            ->assertSee('ok');
    }

    public function test_idle_admin_mfa_session_requires_reverification_on_full_page_navigation(): void
    {
        Route::middleware(EnsureAdminMfa::class)
            ->get('/_admin-mfa-idle-test', fn () => response('ok'));

        config([
            'security.admin_mfa.idle_seconds' => 7200,
            'security.admin_mfa.absolute_seconds' => 43200,
        ]);

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession([
                'admin_mfa_verified_at' => time() - 8000,
                'admin_mfa_last_activity_at' => time() - 7201,
                'admin_mfa_user_id' => $admin->id,
            ])
            ->get('/_admin-mfa-idle-test')
            ->assertRedirect(route('admin.mfa.challenge'));
    }

    public function test_absolute_mfa_lifetime_still_forces_reverification_on_full_page_navigation(): void
    {
        Route::middleware(EnsureAdminMfa::class)
            ->get('/_admin-mfa-absolute-test', fn () => response('ok'));

        config([
            'security.admin_mfa.idle_seconds' => 7200,
            'security.admin_mfa.absolute_seconds' => 43200,
        ]);

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession([
                'admin_mfa_verified_at' => time() - 43201,
                'admin_mfa_last_activity_at' => time() - 10,
                'admin_mfa_user_id' => $admin->id,
            ])
            ->get('/_admin-mfa-absolute-test')
            ->assertRedirect(route('admin.mfa.challenge'));
    }

    public function test_verified_admin_livewire_action_is_not_interrupted_by_mfa_timer_expiry(): void
    {
        Route::middleware(EnsureAdminMfa::class)
            ->post('/livewire/_admin-mfa-relaxed-test', fn () => response('bulk-action-ok'));

        config([
            'security.admin_mfa.idle_seconds' => 7200,
            'security.admin_mfa.absolute_seconds' => 43200,
        ]);

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession([
                'admin_mfa_verified_at' => time() - 50000,
                'admin_mfa_last_activity_at' => time() - 8000,
                'admin_mfa_user_id' => $admin->id,
            ])
            ->withHeader('X-Livewire', 'true')
            ->post('/livewire/_admin-mfa-relaxed-test')
            ->assertOk()
            ->assertSee('bulk-action-ok');
    }

    public function test_livewire_request_without_mfa_for_current_admin_is_still_rejected(): void
    {
        Route::middleware(EnsureAdminMfa::class)
            ->post('/livewire/_admin-mfa-unverified-test', fn () => response('must-not-run'));

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withHeader('X-Livewire', 'true')
            ->post('/livewire/_admin-mfa-unverified-test')
            ->assertStatus(401)
            ->assertJson([
                'code' => 'ADMIN_MFA_REQUIRED',
                'redirect' => route('admin.mfa.challenge'),
            ]);
    }

    public function test_mfa_session_is_bound_to_the_authenticated_admin(): void
    {
        Route::middleware(EnsureAdminMfa::class)
            ->get('/_admin-mfa-bound-test', fn () => response('ok'));

        $admin = User::factory()->create(['is_admin' => true]);
        $otherAdmin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->withSession([
                'admin_mfa_verified_at' => time(),
                'admin_mfa_last_activity_at' => time(),
                'admin_mfa_user_id' => $otherAdmin->id,
            ])
            ->get('/_admin-mfa-bound-test')
            ->assertRedirect(route('admin.mfa.challenge'));
    }

    public function test_admin_can_switch_accounts_from_mfa_challenge(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post(route('admin.mfa.logout'))
            ->assertRedirect('/admin/login');

        $this->assertGuest();
    }

    public function test_mfa_challenge_sends_the_branded_security_mail(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.mfa.challenge'))
            ->assertOk();

        Mail::assertSent(AdminMfaCodeMail::class, fn (AdminMfaCodeMail $mail): bool =>
            strlen($mail->code) === 6 && $mail->expiresInMinutes === 10
        );
    }
}
