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

        $featuredProducts = Product::query()
            ->with(['primaryImage', 'categories'])
            ->featuredPicks()
            ->when($categoryId, function ($query) use ($categoryId): void {
                $query->whereHas('categories', fn ($categoryQuery) => $categoryQuery->whereKey($categoryId));
            })
            ->get();

        $availableProducts = Product::query()
            ->published()
            ->where('is_featured', false)
            ->when($categoryId, function ($query) use ($categoryId): void {
                $query->whereHas('categories', fn ($categoryQuery) => $categoryQuery->whereKey($categoryId));
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.content.product-management.featured-deals.index', compact(
            'featuredCategories',
            'categoryId',
            'featuredProducts',
            'availableProducts',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $product->update([
            'is_featured' => true,
            'featured_sort_order' => ((int) Product::max('featured_sort_order')) + 1,
        ]);

        return $this->redirectToIndex($request, $product->name . ' added to Featured Comfort Deals.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $product->update([
            'is_featured' => false,
            'featured_sort_order' => 0,
        ]);

        return $this->redirectToIndex($request, $product->name . ' removed from Featured Comfort Deals.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:products,id'],
        ]);

        foreach ($validated['order'] as $index => $productId) {
            Product::whereKey($productId)->update([
                'is_featured' => true,
                'featured_sort_order' => $index + 1,
            ]);
        }

        return $this->redirectToIndex($request, 'Featured Comfort Deals order saved.');
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
