<?php

namespace Tests\Unit;

use App\Services\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    public function test_store_resized_outputs_webp_under_100kb(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('Ergonomic Seat Cushion Photo.jpg', 2400, 1800);
        $optimizer = new ImageOptimizer();

        $path = $optimizer->storeResized($file, 'products', 1200);

        Storage::disk('public')->assertExists($path);
        $this->assertStringEndsWith('.webp', $path);
        $this->assertStringContainsString('ergonomic-seat-cushion-photo', $path);
        $this->assertLessThanOrEqual(
            ImageOptimizer::MAX_BYTES,
            Storage::disk('public')->size($path)
        );
    }

    public function test_optimize_existing_converts_jpeg_to_webp_and_shrinks(): void
    {
        Storage::fake('public');

        $jpeg = imagecreatetruecolor(1600, 1200);
        $absolute = Storage::disk('public')->path('products/legacy.jpg');
        Storage::disk('public')->makeDirectory('products');
        imagejpeg($jpeg, $absolute, 95);
        imagedestroy($jpeg);

        $optimizer = new ImageOptimizer();
        $newPath = $optimizer->optimizeExisting('products/legacy.jpg', 1200);

        $this->assertNotNull($newPath);
        $this->assertSame('products/legacy.webp', $newPath);
        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing('products/legacy.jpg');
        $this->assertLessThanOrEqual(
            ImageOptimizer::MAX_BYTES,
            Storage::disk('public')->size($newPath)
        );
    }
}
