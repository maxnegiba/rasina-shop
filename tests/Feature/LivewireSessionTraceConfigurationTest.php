<?php

namespace Tests\Feature;

use Tests\TestCase;

class LivewireSessionTraceConfigurationTest extends TestCase
{
    public function test_livewire_trace_middleware_is_registered_and_hashes_sensitive_values(): void
    {
        $bootstrap = file_get_contents(base_path('bootstrap/app.php'));
        $middleware = file_get_contents(app_path('Http/Middleware/TraceLivewireSession.php'));

        $this->assertIsString($bootstrap);
        $this->assertIsString($middleware);

        $this->assertStringContainsString('TraceLivewireSession::class', $bootstrap);
        $this->assertStringContainsString("substr(hash('sha256', \$value), 0, 12)", $middleware);
        $this->assertStringNotContainsString("'session_id' =>", $middleware);
        $this->assertStringNotContainsString("'session_token' =>", $middleware);
        $this->assertStringNotContainsString("'input_token' =>", $middleware);
    }
}
