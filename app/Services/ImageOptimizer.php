<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ImageOptimizer
{
    /**
     * Store an uploaded image resized/compressed for web delivery.
     * Falls back to a normal store() when GD cannot process the file.
     */
    public function storeResized(
        UploadedFile $file,
        string $directory,
        int $maxWidth = 1200,
        int $quality = 82
    ): string {
        try {
            if (! extension_loaded('gd') || ! function_exists('imagecreatefromstring')) {
                return $file->store($directory, 'public');
            }

            $binary = @file_get_contents($file->getRealPath());
            if ($binary === false) {
                return $file->store($directory, 'public');
            }

            $source = @imagecreatefromstring($binary);
            if ($source === false) {
                return $file->store($directory, 'public');
            }

            $origWidth = imagesx($source);
            $origHeight = imagesy($source);

            if ($origWidth < 1 || $origHeight < 1) {
                imagedestroy($source);

                return $file->store($directory, 'public');
            }

            if ($origWidth > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) max(1, round($origHeight * ($maxWidth / $origWidth)));
            } else {
                $newWidth = $origWidth;
                $newHeight = $origHeight;
            }

            $canvas = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, $transparent);
            imagealphablending($canvas, true);

            imagecopyresampled(
                $canvas,
                $source,
                0,
                0,
                0,
                0,
                $newWidth,
                $newHeight,
                $origWidth,
                $origHeight
            );

            Storage::disk('public')->makeDirectory($directory);

            $preferWebp = function_exists('imagewebp');
            $filename = Str::random(40) . ($preferWebp ? '.webp' : '.jpg');
            $relativePath = trim($directory, '/') . '/' . $filename;
            $absolutePath = Storage::disk('public')->path($relativePath);

            $saved = false;
            if ($preferWebp) {
                $saved = imagewebp($canvas, $absolutePath, $quality);
            }

            if (! $saved) {
                $filename = Str::random(40) . '.jpg';
                $relativePath = trim($directory, '/') . '/' . $filename;
                $absolutePath = Storage::disk('public')->path($relativePath);
                $opaque = imagecreatetruecolor($newWidth, $newHeight);
                $white = imagecolorallocate($opaque, 255, 255, 255);
                imagefilledrectangle($opaque, 0, 0, $newWidth, $newHeight, $white);
                imagecopy($opaque, $canvas, 0, 0, 0, 0, $newWidth, $newHeight);
                $saved = imagejpeg($opaque, $absolutePath, $quality);
                imagedestroy($opaque);
            }

            imagedestroy($canvas);
            imagedestroy($source);

            if (! $saved) {
                return $file->store($directory, 'public');
            }

            return $relativePath;
        } catch (Throwable $exception) {
            report($exception);

            return $file->store($directory, 'public');
        }
    }

    /**
     * Resize an existing public-disk image in place when it exceeds max width.
     */
    public function optimizeExisting(string $relativePath, int $maxWidth = 1200, int $quality = 82): bool
    {
        if (! extension_loaded('gd') || ! function_exists('imagecreatefromstring')) {
            return false;
        }

        if (! Storage::disk('public')->exists($relativePath)) {
            return false;
        }

        $absolutePath = Storage::disk('public')->path($relativePath);
        $binary = @file_get_contents($absolutePath);
        if ($binary === false) {
            return false;
        }

        $source = @imagecreatefromstring($binary);
        if ($source === false) {
            return false;
        }

        $origWidth = imagesx($source);
        $origHeight = imagesy($source);

        if ($origWidth <= $maxWidth) {
            imagedestroy($source);

            return false;
        }

        $newWidth = $maxWidth;
        $newHeight = (int) max(1, round($origHeight * ($maxWidth / $origWidth)));
        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($canvas, true);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $saved = match ($extension) {
            'png' => imagepng($canvas, $absolutePath, 6),
            'webp' => function_exists('imagewebp') ? imagewebp($canvas, $absolutePath, $quality) : imagejpeg($canvas, $absolutePath, $quality),
            default => imagejpeg($canvas, $absolutePath, $quality),
        };

        imagedestroy($canvas);
        imagedestroy($source);

        return (bool) $saved;
    }
}
