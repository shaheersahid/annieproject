<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'featured_category_ids')) {
                $table->json('featured_category_ids')->nullable()->after('featured_sort_order');
            }
        });

        $homeCategoryIds = Category::query()
            ->active()
            ->parentCategories()
            ->ordered()
            ->take(2)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        Product::query()
            ->where('is_featured', true)
            ->with('categories:id')
            ->each(function (Product $product) use ($homeCategoryIds): void {
                $matched = $product->categories
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->intersect($homeCategoryIds)
                    ->values()
                    ->all();

                $product->forceFill([
                    'featured_category_ids' => $matched !== [] ? $matched : $homeCategoryIds,
                ])->saveQuietly();
            });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'featured_category_ids')) {
                $table->dropColumn('featured_category_ids');
            }
        });
    }
};
