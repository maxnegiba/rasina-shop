<?php

namespace Tests\Unit;

use App\Services\SafeHtmlSanitizer;
use Tests\TestCase;

class SafeHtmlSanitizerTest extends TestCase
{
    public function test_it_removes_active_content_and_dangerous_urls(): void
    {
        $html = '<p onclick="alert(1)">Salut <strong>lume</strong></p>'
            .'<script>alert(1)</script>'
            .'<a href="javascript:alert(1)" target="_blank">bad</a>'
            .'<a href="https://example.com" target="_blank">good</a>';

        $sanitized = app(SafeHtmlSanitizer::class)->sanitize($html);

        $this->assertStringContainsString('<strong>lume</strong>', $sanitized);
        $this->assertStringNotContainsString('<script', $sanitized);
        $this->assertStringNotContainsString('onclick=', $sanitized);
        $this->assertStringNotContainsString('javascript:', $sanitized);
        $this->assertStringContainsString('href="https://example.com"', $sanitized);
        $this->assertStringContainsString('rel="noopener noreferrer"', $sanitized);
    }
}
