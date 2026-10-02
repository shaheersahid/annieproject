<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Image;
use App\Models\Seller;
use App\Services\ImageOptimizer;
use Illuminate\Console\Command;

class OptimizeStorefrontImages extends Command
{
    protected $signature = 'images:optimize-storefront {--dry-run : Report only, do not rewrite files}';

    protected $description = 'Convert storefront images to compressed WebP under 100 KB where possible';

    public function handle(ImageOptimizer $optimizer): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $optimized = 0;

        Image::query()
            ->where(function ($query): void {
                $query->where('path', 'like', 'categories/%')
                    ->orWhere('path', 'like', 'products/%');
            })
            ->orderBy('id')
            ->chunkById(50, function ($images) use ($optimizer, $dryRun, &$optimized): void {
                foreach ($images as $image) {
                    $maxWidth = str_starts_with((string) $image->path, 'categories/') ? 800 : 1200;
                    $optimized += $this->optimizePath(
                        $optimizer,
                        (string) $image->path,
                        $maxWidth,
                        $dryRun,
                        function (string $newPath) use ($image): void {
                            $image->update(['path' => $newPath]);
                        }
                    );
                }
            });

        BlogPost::query()
            ->whereNotNull('featured_image')
            ->orderBy('id')
            ->chunkById(50, function ($posts) use ($optimizer, $dryRun, &$optimized): void {
                foreach ($posts as $post) {
                    $optimized += $this->optimizePath(
                        $optimizer,
                        (string) $post->featured_image,
                        1200,
                        $dryRun,
                        function (string $newPath) use ($post): void {
                            $post->update(['featured_image' => $newPath]);
                        }
                    );
                }
            });

        Seller::query()
            ->where(function ($query): void {
                $query->whereNotNull('store_logo')->orWhereNotNull('cover_photo');
            })
            ->orderBy('id')
            ->chunkById(50, function ($sellers) use ($optimizer, $dryRun, &$optimized): void {
                foreach ($sellers as $seller) {
                    if ($seller->store_logo) {
                        $optimized += $this->optimizePath(
                            $optimizer,
                            (string) $seller->store_logo,
                            800,
                            $dryRun,
                            function (string $newPath) use ($seller): void {
                                $seller->update(['store_logo' => $newPath]);
                            }
                        );
                    }

                    if ($seller->cover_photo) {
                        $optimized += $this->optimizePath(
                            $optimizer,
                            (string) $seller->cover_photo,
                            1400,
                            $dryRun,
                            function (string $newPath) use ($seller): void {
                                $seller->update(['cover_photo' => $newPath]);
                            }
                        );
                    }
                }
            });

        $this->info($dryRun
            ? "Dry run complete. {$optimized} image(s) would be checked/optimized."
            : "Done. {$optimized} image(s) optimized.");

        return self::SUCCESS;
    }

    private function optimizePath(
        ImageOptimizer $optimizer,
        string $path,
        int $maxWidth,
        bool $dryRun,
        callable $onPathChanged
    ): int {
        if ($dryRun) {
            $this->line("Would optimize: {$path} (max {$maxWidth}px, <= 100KB WebP)");

            return 1;
        }

        $newPath = $optimizer->optimizeExisting($path, $maxWidth);

        if ($newPath === null) {
            return 0;
        }

        if ($newPath !== $path) {
            $onPathChanged($newPath);
            $this->info("Optimized + converted: {$path} → {$newPath}");
        } else {
            $this->info("Optimized: {$path}");
        }

        return 1;
    }
}
