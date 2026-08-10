<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HomePagePerformanceRegressionTest extends TestCase
{
    public function test_homepage_fonts_are_discoverable_without_a_late_stylesheet_swap(): void
    {
        $layout = file_get_contents(__DIR__.'/../../resources/views/layouts/app.blade.php');

        $this->assertIsString($layout);
        $this->assertStringContainsString('<style data-home-fonts>', $layout);
        $this->assertStringContainsString('as="font"', $layout);
        $this->assertStringContainsString('font-display: optional', $layout);
        $this->assertStringContainsString("style[data-home-manrope-fonts]", $layout);
        $this->assertStringContainsString("window.addEventListener('load', loadHomeManrope", $layout);
        $this->assertStringNotContainsString('$homeCormorantStylesheet', $layout);
    }

    public function test_desktop_hero_is_not_forced_into_a_single_unbreakable_line(): void
    {
        $criticalCss = file_get_contents(__DIR__.'/../../resources/css/home-critical.css');

        $this->assertIsString($criticalCss);
        $this->assertStringNotContainsString('white-space: nowrap', $criticalCss);
    }

    public function test_homepage_keeps_small_product_candidates_and_accessible_badges(): void
    {
        $home = file_get_contents(__DIR__.'/../../resources/views/home.blade.php');

        $this->assertIsString($home);
        $this->assertStringContainsString('[192, 320, 480, 720]', $home);
        $this->assertStringContainsString('bg-[#70511f] text-white', $home);
        $this->assertStringNotContainsString('bg-vintage-gold text-white text-xs', $home);
    }

    public function test_confirmed_desktop_lcp_image_has_high_fetch_priority(): void
    {
        $hero = file_get_contents(__DIR__.'/../../resources/views/components/atelier-hero.blade.php');

        $this->assertIsString($hero);
        $this->assertStringContainsString("\$piece['key'] === 'natural-materials' ? 'high' : 'low'", $hero);
    }
}
