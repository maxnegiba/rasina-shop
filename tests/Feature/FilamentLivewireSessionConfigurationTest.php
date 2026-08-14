<?php

namespace Tests\Feature;

use Tests\TestCase;

class FilamentLivewireSessionConfigurationTest extends TestCase
{
    public function test_panel_and_auth_middleware_are_not_forced_to_persist_on_livewire_requests(): void
    {
        $source = file_get_contents(app_path('Providers/Filament/AdminPanelProvider.php'));

        $this->assertIsString($source);

        $this->assertStringNotContainsString(
            "DispatchServingFilamentEvent::class,\n            ], isPersistent: true)",
            $source,
            'The full cookie/session/CSRF panel middleware stack must not be registered as persistent Livewire middleware.',
        );

        $this->assertStringNotContainsString(
            "EnsureAdminMfa::class,\n            ], isPersistent: true)",
            $source,
            'Authenticated panel middleware must not be forced to persist across Livewire requests because persistent auth/session mutation can break the temporary file upload lifecycle.',
        );

        $this->assertStringContainsString(
            "->authMiddleware([\n                Authenticate::class,\n                EnsureAdminMfa::class,\n            ])",
            $source,
            'Admin authentication and MFA must still protect full authenticated panel requests.',
        );
    }

    public function test_session_guard_does_not_turn_a_livewire_419_into_an_admin_login_redirect(): void
    {
        $source = file_get_contents(resource_path('views/filament/admin/session-guard.blade.php'));

        $this->assertIsString($source);
        $this->assertStringNotContainsString("status === 419", $source);
        $this->assertStringNotContainsString("redirectOnce('/admin/login');", $source);
    }
}
