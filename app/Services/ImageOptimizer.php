<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ImageOptimizer
{
    public const MAX_BYTES = 102400; // 100 KB

    /**
     * Store an uploaded image as compressed WebP (prefer < 100 KB).
     * Falls back to JPEG, then to a normal store() when GD cannot process the file.
     */
    public function storeResized(
        UploadedFile $file,
        string $directory,
        int $maxWidth = 1200,
        int $quality = 82,
        int $maxBytes = self::MAX_BYTES,
        ?string $seoBaseName = null
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

            Storage::disk('public')->makeDirectory($directory);

            $baseName = $this->seoBaseName($seoBaseName ?? $file->getClientOriginalName());
            $preferWebp = function_exists('imagewebp');

            $encoded = $this->encodeUnderBudget(
                $source,
                $origWidth,
                $origHeight,
                $maxWidth,
                $quality,
                $maxBytes,
                $preferWebp
            );

            imagedestroy($source);

            if ($encoded === null) {
                return $file->store($directory, 'public');
            }

            $filename = $baseName . '.' . $encoded['extension'];
            $relativePath = trim($directory, '/') . '/' . $filename;
            $absolutePath = Storage::disk('public')->path($relativePath);

            // Avoid collisions with an existing file of the same SEO slug.
            if (is_file($absolutePath)) {
                $filename = $baseName . '-' . Str::lower(Str::random(6)) . '.' . $encoded['extension'];
                $relativePath = trim($directory, '/') . '/' . $filename;
                $absolutePath = Storage::disk('public')->path($relativePath);
            }

            if (@file_put_contents($absolutePath, $encoded['binary']) === false) {
                return $file->store($directory, 'public');
            }

            return $relativePath;
        } catch (Throwable $exception) {
            report($exception);

            return $file->store($directory, 'public');
        }
    }

    /**
     * Re-encode an existing public-disk image to WebP under the byte budget.
     * Returns the (possibly new) relative path when conversion changes the extension.
     */
    public function optimizeExisting(
        string $relativePath,
        int $maxWidth = 1200,
        int $quality = 82,
        int $maxBytes = self::MAX_BYTES
    ): ?string {
        if (! extension_loaded('gd') || ! function_exists('imagecreatefromstring')) {
            return null;
        }

        if (! Storage::disk('public')->exists($relativePath)) {
            return null;
        }

        $absolutePath = Storage::disk('public')->path($relativePath);
        $currentSize = @filesize($absolutePath) ?: 0;
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $alreadyWebpUnderBudget = $extension === 'webp'
            && $currentSize > 0
            && $currentSize <= $maxBytes;

        $binary = @file_get_contents($absolutePath);
        if ($binary === false) {
            return null;
        }

        $source = @imagecreatefromstring($binary);
        if ($source === false) {
            return null;
        }

        $origWidth = imagesx($source);
        $origHeight = imagesy($source);

        if ($origWidth < 1 || $origHeight < 1) {
            imagedestroy($source);

            return null;
        }

        // Skip work when already WebP, under budget, and within max width.
        if ($alreadyWebpUnderBudget && $origWidth <= $maxWidth) {
            imagedestroy($source);

            return null;
        }

        $preferWebp = function_exists('imagewebp');
        $encoded = $this->encodeUnderBudget(
            $source,
            $origWidth,
            $origHeight,
            $maxWidth,
            $quality,
            $maxBytes,
            $preferWebp
        );

        imagedestroy($source);

        if ($encoded === null) {
            return null;
        }

        $directory = trim(str_replace('\\', '/', dirname($relativePath)), '.');
        $directory = $directory === '' ? '' : trim($directory, '/');
        $baseName = pathinfo($relativePath, PATHINFO_FILENAME);
        $newRelative = ($directory !== '' ? $directory . '/' : '') . $baseName . '.' . $encoded['extension'];
        $newAbsolute = Storage::disk('public')->path($newRelative);

        if (@file_put_contents($newAbsolute, $encoded['binary']) === false) {
            return null;
        }

        if ($newRelative !== $relativePath) {
            Storage::disk('public')->delete($relativePath);
        }

        return $newRelative;
    }

    /**
     * @return array{binary: string, extension: string}|null
     */
    private function encodeUnderBudget(
        $source,
        int $origWidth,
        int $origHeight,
        int $maxWidth,
        int $startQuality,
        int $maxBytes,
        bool $preferWebp
    ): ?array {
        $width = $origWidth > $maxWidth ? $maxWidth : $origWidth;
        $qualities = $this->qualitySteps($startQuality);
        $minWidth = 480;
        $best = null;

        while ($width >= $minWidth) {
            $height = (int) max(1, round($origHeight * ($width / $origWidth)));
            $canvas = $this->resample($source, $origWidth, $origHeight, $width, $height);

            foreach ($qualities as $quality) {
                $attempt = $preferWebp
                    ? $this->encodeWebp($canvas, $quality)
                    : $this->encodeJpeg($canvas, $quality);

                if ($attempt === null) {
                    continue;
                }

                $best = $attempt;

                if (strlen($attempt['binary']) <= $maxBytes) {
                    imagedestroy($canvas);

                    return $attempt;
                }
            }

            imagedestroy($canvas);

            // Still too large — shrink dimensions and retry.
            $nextWidth = (int) max($minWidth, round($width * 0.85));
            if ($nextWidth >= $width) {
                break;
            }
            $width = $nextWidth;
        }

        // Final pass at minimum width / lowest quality; keep best effort even if slightly over budget.
        return $best;
    }

    /**
     * @return list<int>
     */
    private function qualitySteps(int $startQuality): array
    {
        $steps = [];
        for ($q = min(90, max(40, $startQuality)); $q >= 40; $q -= 8) {
            $steps[] = $q;
        }

        if (! in_array(40, $steps, true)) {
            $steps[] = 40;
        }

        return array_values(array_unique($steps));
    }

    private function resample($source, int $origWidth, int $origHeight, int $newWidth, int $newHeight)
    {
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

        return $canvas;
    }

    /**
     * @return array{binary: string, extension: string}|null
     */
    private function encodeWebp($canvas, int $quality): ?array
    {
        if (! function_exists('imagewebp')) {
            return $this->encodeJpeg($canvas, $quality);
        }

        ob_start();
        $saved = imagewebp($canvas, null, $quality);
        $binary = ob_get_clean();

        if (! $saved || $binary === false || $binary === '') {
            return null;
        }

        return ['binary' => $binary, 'extension' => 'webp'];
    }

    /**
     * @return array{binary: string, extension: string}|null
     */
    private function encodeJpeg($canvas, int $quality): ?array
    {
        $width = imagesx($canvas);
        $height = imagesy($canvas);
        $opaque = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($opaque, 255, 255, 255);
        imagefilledrectangle($opaque, 0, 0, $width, $height, $white);
        imagecopy($opaque, $canvas, 0, 0, 0, 0, $width, $height);

        ob_start();
        $saved = imagejpeg($opaque, null, $quality);
        $binary = ob_get_clean();
        imagedestroy($opaque);

        if (! $saved || $binary === false || $binary === '') {
            return null;
        }

        return ['binary' => $binary, 'extension' => 'jpg'];
    }

    private function seoBaseName(string $originalName): string
    {
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $slug = Str::slug($name);
        $slug = $slug !== '' ? $slug : 'image';

        return Str::limit($slug, 60, '') . '-' . Str::lower(Str::random(8));
    }
}
