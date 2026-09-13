<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $homeCategoryIds = Category::query()
            ->active()
            ->parentCategories()
            ->ordered()
            ->take(2)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($homeCategoryIds === []) {
            return;
        }

        Product::query()
            ->where('is_featured', true)
            ->with('categories:id')
            ->each(function (Product $product) use ($homeCategoryIds): void {
                $current = collect($product->featured_category_ids ?? [])
                    ->map(fn ($id) => (int) $id)
                    ->filter()
                    ->unique()
                    ->values();

                $productCategoryIds = $product->categories
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->intersect($homeCategoryIds)
                    ->values();

                // If both homepage tabs were auto-assigned, narrow to the product's real categories.
                $assignedAllHomeTabs = collect($homeCategoryIds)->diff($current)->isEmpty()
                    && $current->count() >= count($homeCategoryIds);

                if ($assignedAllHomeTabs && $productCategoryIds->isNotEmpty()) {
                    $current = $productCategoryIds;
                } elseif ($current->isEmpty() && $productCategoryIds->isNotEmpty()) {
                    $current = $productCategoryIds;
                } elseif ($current->isEmpty()) {
                    $current = collect();
                } else {
                    $current = $current->intersect($homeCategoryIds)->values();
                }

                $product->forceFill([
                    'featured_category_ids' => $current->all(),
                    'is_featured' => $current->isNotEmpty(),
                    'featured_sort_order' => $current->isNotEmpty() ? ($product->featured_sort_order ?: 0) : 0,
                ])->saveQuietly();
            });
    }

    public function down(): void
    {
        //
    }
};
