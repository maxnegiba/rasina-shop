<?php

namespace App\Support;

final class OptimizedImage
{
    public static function public(string $path, int $width, int $quality = 74): string
    {
        return self::url('public:'.ltrim($path, '/'), $width, $quality);
    }

    public static function storage(string $path, int $width, int $quality = 72): string
    {
        return self::url('storage:'.ltrim($path, '/'), $width, $quality);
    }

    public static function srcset(string $source, array $widths, int $quality = 72): string
    {
        return collect($widths)
            ->map(static fn (int $width): string => self::url($source, $width, $quality).' '.$width.'w')
            ->implode(', ');
    }

    public static function url(string $source, int $width, int $quality = 72): string
    {
        $encoded = rtrim(strtr(base64_encode($source), '+/', '-_'), '=');

        return route('media.optimized', [
            'encoded' => $encoded,
            'w' => max(32, min(1600, $width)),
            'q' => max(45, min(88, $quality)),
        ]);
    }
}
