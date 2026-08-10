<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PrivateImageUploadService
{
    private const MAX_PIXELS = 20_000_000;

    public function store(UploadedFile $file, string $directory = 'custom_requests'): string
    {
        $info = @getimagesize($file->getRealPath());

        if (! $info || empty($info[0]) || empty($info[1])) {
            throw new RuntimeException('Imaginea încărcată nu poate fi procesată.');
        }

        [$width, $height, $type] = $info;

        if (($width * $height) > self::MAX_PIXELS) {
            throw new RuntimeException('Imaginea are o rezoluție prea mare.');
        }

        $binary = file_get_contents($file->getRealPath());
        $image = $binary !== false ? @imagecreatefromstring($binary) : false;

        if (! $image) {
            throw new RuntimeException('Imaginea încărcată nu poate fi decodată.');
        }

        if ($type === IMAGETYPE_JPEG) {
            $image = $this->applyJpegOrientation($image, $file->getRealPath());
        }

        $extension = match ($type) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
            default => throw new RuntimeException('Formatul imaginii nu este acceptat.'),
        };

        $path = trim($directory, '/').'/'.bin2hex(random_bytes(20)).'.'.$extension;
        $stream = fopen('php://temp', 'w+b');

        try {
            $written = match ($type) {
                IMAGETYPE_JPEG => imagejpeg($image, $stream, 90),
                IMAGETYPE_PNG => imagepng($image, $stream, 6),
                IMAGETYPE_WEBP => function_exists('imagewebp') && imagewebp($image, $stream, 90),
                default => false,
            };

            if (! $written) {
                throw new RuntimeException('Imaginea nu a putut fi securizată.');
            }

            rewind($stream);

            if (! Storage::disk('local')->put($path, $stream)) {
                throw new RuntimeException('Imaginea nu a putut fi salvată în spațiul privat.');
            }
        } finally {
            imagedestroy($image);
            fclose($stream);
        }

        return $path;
    }

    private function applyJpegOrientation(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $orientation = (int) ($exif['Orientation'] ?? 1);
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => false,
        };

        if ($rotated instanceof \GdImage) {
            imagedestroy($image);

            return $rotated;
        }

        return $image;
    }
}
