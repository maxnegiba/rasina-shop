<?php

namespace Tests\Feature;

use Tests\TestCase;

class OptimizedImageControllerTest extends TestCase
{
    public function test_logo_derivative_is_publicly_cacheable(): void
    {
        $encoded = rtrim(strtr(base64_encode('public:img/logo.png'), '+/', '-_'), '=');

        $response = $this->get('/media/optimized/'.$encoded.'?w=192&q=76');

        $response->assertOk();
        $response->assertHeader('Cache-Control', 'public, max-age=31536000, immutable');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_public_media_route_cannot_escape_the_img_directory(): void
    {
        $encoded = rtrim(strtr(base64_encode('public:.env'), '+/', '-_'), '=');

        $this->get('/media/optimized/'.$encoded.'?w=192&q=76')->assertNotFound();
    }

    public function test_storage_media_route_rejects_path_traversal(): void
    {
        $encoded = rtrim(strtr(base64_encode('storage:../.env'), '+/', '-_'), '=');

        $this->get('/media/optimized/'.$encoded.'?w=480&q=72')->assertNotFound();
    }
}
