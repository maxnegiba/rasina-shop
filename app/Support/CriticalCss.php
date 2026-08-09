<?php

namespace App\Support;

final class CriticalCss
{
    /** @var array<string, string|null> */
    private static array $cache = [];

    public static function fromViteManifest(string $entry, ?string $buildDirectory = null): ?string
    {
        $usesDefaultBuildDirectory = $buildDirectory === null;
        $buildDirectory = $usesDefaultBuildDirectory ? public_path('build') : $buildDirectory;
        $cacheKey = $buildDirectory.'|'.$entry;

        if (array_key_exists($cacheKey, self::$cache)) {
            return self::$cache[$cacheKey];
        }

        if ($usesDefaultBuildDirectory && is_file(public_path('hot'))) {
            return self::$cache[$cacheKey] = null;
        }

        $manifestPath = rtrim($buildDirectory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'manifest.json';

        if (! is_file($manifestPath) || ! is_readable($manifestPath)) {
            return self::$cache[$cacheKey] = null;
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        $assetFile = is_array($manifest) ? ($manifest[$entry]['file'] ?? null) : null;

        if (! is_string($assetFile) || $assetFile === '' || ! str_ends_with($assetFile, '.css')) {
            return self::$cache[$cacheKey] = null;
        }

        $buildRoot = realpath($buildDirectory);
        $assetPath = realpath(rtrim($buildDirectory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.ltrim($assetFile, '/\\'));

        if ($buildRoot === false || $assetPath === false) {
            return self::$cache[$cacheKey] = null;
        }

        $buildPrefix = rtrim($buildRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        if (! str_starts_with($assetPath, $buildPrefix) || ! is_readable($assetPath)) {
            return self::$cache[$cacheKey] = null;
        }

        $css = file_get_contents($assetPath);

        return self::$cache[$cacheKey] = is_string($css) && $css !== '' ? $css : null;
    }
}
