<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureAdminMfa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
                'admin_mfa_user_id' => $admin->id,
            ])
            ->get('/_admin-mfa-verified-test')
            ->assertOk()
            ->assertSee('ok');
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
                'admin_mfa_user_id' => $otherAdmin->id,
            ])
            ->get('/_admin-mfa-bound-test')
            ->assertRedirect(route('admin.mfa.challenge'));
    }
}
