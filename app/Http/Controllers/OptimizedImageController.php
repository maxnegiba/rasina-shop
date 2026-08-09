<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class OptimizedImageController extends Controller
{
    public function __invoke(Request $request, string $encoded): BinaryFileResponse
    {
        $source = $this->decodeSource($encoded);
        $sourcePath = $this->resolveSourcePath($source);

        abort_unless($sourcePath && is_file($sourcePath), 404);

        $width = max(32, min(1600, $request->integer('w', 480)));
        $quality = max(45, min(88, $request->integer('q', 72)));
        $mtime = (int) filemtime($sourcePath);
        $cacheKey = hash('sha256', $source.'|'.$mtime.'|'.$width.'|'.$quality);
        $cacheDir = storage_path('app/image-cache');
        $optimizedPath = $cacheDir.DIRECTORY_SEPARATOR.$cacheKey.'.webp';

        if (! is_file($optimizedPath)) {
            if (! is_dir($cacheDir)) {
                @mkdir($cacheDir, 0755, true);
            }

            if (is_dir($cacheDir)) {
                $this->generateWebp($sourcePath, $optimizedPath, $width, $quality);
            }
        }

        $servedPath = is_file($optimizedPath) ? $optimizedPath : $sourcePath;
        $response = response()->file($servedPath);
        $response->setPublic();
        $response->setMaxAge(31536000);
        $response->setSharedMaxAge(31536000);
        $response->setLastModified(new \DateTimeImmutable('@'.filemtime($servedPath)));
        $response->setEtag(hash_file('sha256', $servedPath));
        $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->isNotModified($request);

        return $response;
    }

    private function decodeSource(string $encoded): string
    {
        $padding = strlen($encoded) % 4;
        if ($padding !== 0) {
            $encoded .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode(strtr($encoded, '-_', '+/'), true);

        abort_unless(is_string($decoded) && $decoded !== '', 404);

        return $decoded;
    }

    private function resolveSourcePath(string $source): ?string
    {
        if (str_starts_with($source, 'public:')) {
            $relative = substr($source, 7);
            if (! str_starts_with($relative, 'img/')) {
                return null;
            }

            return $this->safePath(public_path(), $relative);
        }

        if (str_starts_with($source, 'storage:')) {
            $relative = substr($source, 8);
            $resolved = $this->safePath(storage_path('app/public'), $relative);

            if ($resolved) {
                return $resolved;
            }

            return $this->safePath(public_path('storage'), $relative);
        }

        return null;
    }

    private function safePath(string $base, string $relative): ?string
    {
        $relative = ltrim(str_replace('\\', '/', $relative), '/');

        if ($relative === '' || str_contains($relative, '..')) {
            return null;
        }

        $extension = strtolower(pathinfo($relative, PATHINFO_EXTENSION));
        if (! in_array($extension, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            return null;
        }

        $candidate = realpath(rtrim($base, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$relative);
        $baseReal = realpath($base);

        if (! $candidate || ! $baseReal) {
            return null;
        }

        $prefix = rtrim($baseReal, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return str_starts_with($candidate, $prefix) ? $candidate : null;
    }

    private function generateWebp(string $sourcePath, string $targetPath, int $maxWidth, int $quality): bool
    {
        $temporaryPath = $targetPath.'.tmp';

        try {
            if (class_exists(\Imagick::class)) {
                $image = new \Imagick($sourcePath);
                $image->setIteratorIndex(0);
                $image->setImageFormat('webp');
                $image->setImageCompressionQuality($quality);
                $image->stripImage();

                if ($image->getImageWidth() > $maxWidth) {
                    $image->thumbnailImage($maxWidth, 0, true, true);
                }

                $written = $image->writeImage($temporaryPath);
                $image->clear();
                $image->destroy();

                if ($written && is_file($temporaryPath)) {
                    return rename($temporaryPath, $targetPath);
                }
            }
        } catch (Throwable) {
            @unlink($temporaryPath);
        }

        if (! function_exists('imagewebp')) {
            return false;
        }

        $info = @getimagesize($sourcePath);
        if (! is_array($info)) {
            return false;
        }

        [$sourceWidth, $sourceHeight, $type] = $info;
        $sourceImage = match ($type) {
            IMAGETYPE_PNG => function_exists('imagecreatefrompng') ? @imagecreatefrompng($sourcePath) : false,
            IMAGETYPE_JPEG => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($sourcePath) : false,
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            default => false,
        };

        if (! $sourceImage) {
            return false;
        }

        $targetWidth = min($maxWidth, $sourceWidth);
        $targetHeight = max(1, (int) round($sourceHeight * ($targetWidth / $sourceWidth)));
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);
        $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
        imagefill($targetImage, 0, 0, $transparent);

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight,
        );

        $written = @imagewebp($targetImage, $temporaryPath, $quality);
        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        if ($written && is_file($temporaryPath)) {
            return rename($temporaryPath, $targetPath);
        }

        @unlink($temporaryPath);

        return false;
    }
}
