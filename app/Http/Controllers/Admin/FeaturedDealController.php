<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FeaturedDealController extends Controller
{
    public function index(Request $request): View
    {
        $featuredCategories = $this->featuredCategories();
        $categoryId = $request->integer('category') ?: $featuredCategories->first()?->id;
        $activeCategory = $featuredCategories->firstWhere('id', $categoryId) ?? $featuredCategories->first();

        $featuredProducts = Product::query()
            ->with(['primaryImage', 'categories'])
            ->when(
                $categoryId,
                fn ($query) => $query->featuredInCategory((int) $categoryId),
                fn ($query) => $query->featuredPicks()
            )
            ->get();

        $featuredProductIds = $featuredProducts->pluck('id');

        $availableProducts = Product::query()
            ->published()
            ->when(
                $featuredProductIds->isNotEmpty(),
                fn ($query) => $query->whereNotIn('id', $featuredProductIds)
            )
            ->orderBy('name')
            ->get(['id', 'name', 'featured_category_ids', 'is_featured']);

        $tabCounts = $featuredCategories->mapWithKeys(function (Category $category) {
            return [
                $category->id => Product::query()->featuredInCategory($category->id)->count(),
            ];
        });

        return view('admin.content.product-management.featured-deals.index', compact(
            'featuredCategories',
            'categoryId',
            'activeCategory',
            'featuredProducts',
            'availableProducts',
            'tabCounts',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'category' => ['required', 'integer', 'exists:categories,id'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $categoryId = (int) $validated['category'];
        $ids = collect($product->featured_category_ids ?? [])
            ->map(fn ($id) => (int) $id)
            ->push($categoryId)
            ->unique()
            ->values()
            ->all();

        $product->forceFill([
            'is_featured' => true,
            'featured_sort_order' => $product->featured_sort_order
                ?: ((int) Product::max('featured_sort_order')) + 1,
            'featured_category_ids' => $ids,
        ])->save();

        $tabName = Category::find($categoryId)?->name ?? 'this tab';

        return $this->redirectToIndex($request, $product->name . ' added to ' . $tabName . '.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $categoryId = $request->integer('category');
        $ids = collect($product->featured_category_ids ?? [])
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $categoryId && $id === $categoryId)
            ->values()
            ->all();

        if (! $categoryId) {
            $ids = [];
        }

        $product->forceFill([
            'featured_category_ids' => $ids,
            'is_featured' => $ids !== [],
            'featured_sort_order' => $ids !== [] ? $product->featured_sort_order : 0,
        ])->save();

        $tabName = $categoryId ? (Category::find($categoryId)?->name ?? 'this tab') : 'Featured Comfort Deals';

        return $this->redirectToIndex($request, $product->name . ' removed from ' . $tabName . '.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:products,id'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        foreach ($validated['order'] as $index => $productId) {
            Product::whereKey($productId)->update([
                'is_featured' => true,
                'featured_sort_order' => $index + 1,
            ]);
        }

        return $this->redirectToIndex($request, 'Order saved for this tab.');
    }

    private function redirectToIndex(Request $request, string $message): RedirectResponse
    {
        return redirect()
            ->route('admin.featured-deals.index', array_filter([
                'category' => $request->integer('category') ?: null,
            ]))
            ->with('success', $message);
    }

    private function featuredCategories()
    {
        return Category::query()
            ->active()
            ->parentCategories()
            ->ordered()
            ->take(2)
            ->get();
    }
}
