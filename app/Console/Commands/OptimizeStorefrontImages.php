<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Services\ImageOptimizer;
use Illuminate\Console\Command;

class OptimizeStorefrontImages extends Command
{
    protected $signature = 'images:optimize-storefront {--dry-run : Report only, do not rewrite files}';

    protected $description = 'Resize oversized category/product images already stored on the public disk';

    public function handle(ImageOptimizer $optimizer): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $resized = 0;

        Image::query()
            ->where(function ($query): void {
                $query->where('path', 'like', 'categories/%')
                    ->orWhere('path', 'like', 'products/%');
            })
            ->orderBy('id')
            ->chunkById(50, function ($images) use ($optimizer, $dryRun, &$resized): void {
                foreach ($images as $image) {
                    $maxWidth = str_starts_with((string) $image->path, 'categories/') ? 800 : 1200;

                    if ($dryRun) {
                        $this->line("Would optimize: {$image->path} (max {$maxWidth}px)");
                        $resized++;
                        continue;
                    }

                    if ($optimizer->optimizeExisting((string) $image->path, $maxWidth)) {
                        $this->info("Optimized: {$image->path}");
                        $resized++;
                    }
                }
            });

        $this->info($dryRun
            ? "Dry run complete. {$resized} image(s) would be checked/resized."
            : "Done. {$resized} image(s) resized.");

        return self::SUCCESS;
    }
}
