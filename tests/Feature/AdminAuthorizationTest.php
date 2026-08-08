<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_explicit_admin_users_can_access_filament(): void
    {
        /** @var Panel $panel */
        $panel = (new ReflectionClass(Panel::class))->newInstanceWithoutConstructor();
        $regularUser = User::factory()->create();
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->assertFalse($regularUser->canAccessPanel($panel));
        $this->assertTrue($admin->canAccessPanel($panel));
    }
}
