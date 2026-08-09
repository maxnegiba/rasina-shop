<?php

namespace Tests\Unit;

use App\Support\CriticalCss;
use PHPUnit\Framework\TestCase;

class CriticalCssTest extends TestCase
{
    private array $temporaryPaths = [];

    protected function tearDown(): void
    {
        foreach (array_reverse($this->temporaryPaths) as $path) {
            if (is_file($path)) {
                @unlink($path);
            } elseif (is_dir($path)) {
                @rmdir($path);
            }
        }

        parent::tearDown();
    }

    public function test_it_reads_a_css_entry_from_a_vite_manifest(): void
    {
        $buildDirectory = $this->makeBuildDirectory();
        $assetDirectory = $buildDirectory.DIRECTORY_SEPARATOR.'assets';
        mkdir($assetDirectory);
        $this->temporaryPaths[] = $assetDirectory;

        $css = 'body{background:#fffff0}';
        file_put_contents($assetDirectory.DIRECTORY_SEPARATOR.'home-critical-test.css', $css);
        $this->temporaryPaths[] = $assetDirectory.DIRECTORY_SEPARATOR.'home-critical-test.css';

        file_put_contents(
            $buildDirectory.DIRECTORY_SEPARATOR.'manifest.json',
            json_encode([
                'resources/css/home-critical.css' => [
                    'file' => 'assets/home-critical-test.css',
                    'isEntry' => true,
                ],
            ], JSON_THROW_ON_ERROR)
        );
        $this->temporaryPaths[] = $buildDirectory.DIRECTORY_SEPARATOR.'manifest.json';

        self::assertSame(
            $css,
            CriticalCss::fromViteManifest('resources/css/home-critical.css', $buildDirectory)
        );
    }

    public function test_it_returns_null_when_the_manifest_entry_is_missing(): void
    {
        $buildDirectory = $this->makeBuildDirectory();

        file_put_contents($buildDirectory.DIRECTORY_SEPARATOR.'manifest.json', '{}');
        $this->temporaryPaths[] = $buildDirectory.DIRECTORY_SEPARATOR.'manifest.json';

        self::assertNull(
            CriticalCss::fromViteManifest('resources/css/home-critical.css', $buildDirectory)
        );
    }

    public function test_it_rejects_assets_outside_the_build_directory(): void
    {
        $buildDirectory = $this->makeBuildDirectory();
        $outsideCss = dirname($buildDirectory).DIRECTORY_SEPARATOR.'outside-'.bin2hex(random_bytes(4)).'.css';
        file_put_contents($outsideCss, 'body{}');
        $this->temporaryPaths[] = $outsideCss;

        file_put_contents(
            $buildDirectory.DIRECTORY_SEPARATOR.'manifest.json',
            json_encode([
                'resources/css/home-critical.css' => [
                    'file' => '../'.basename($outsideCss),
                    'isEntry' => true,
                ],
            ], JSON_THROW_ON_ERROR)
        );
        $this->temporaryPaths[] = $buildDirectory.DIRECTORY_SEPARATOR.'manifest.json';

        self::assertNull(
            CriticalCss::fromViteManifest('resources/css/home-critical.css', $buildDirectory)
        );
    }

    private function makeBuildDirectory(): string
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'mtdart-critical-css-'.bin2hex(random_bytes(6));
        mkdir($directory);
        $this->temporaryPaths[] = $directory;

        return $directory;
    }
}
