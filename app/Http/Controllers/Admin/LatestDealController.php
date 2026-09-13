<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LatestDealController extends Controller
{
    public function index(): View
    {
        $latestProducts = Product::query()
            ->with(['primaryImage', 'categories'])
            ->latestPicks()
            ->get();

        $availableProducts = Product::query()
            ->published()
            ->where('is_latest', false)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.content.product-management.latest-deals.index', compact(
            'latestProducts',
            'availableProducts',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $product->update([
            'is_latest' => true,
            'latest_sort_order' => ((int) Product::max('latest_sort_order')) + 1,
        ]);

        return back()->with('success', $product->name . ' added to Latest Deals.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->update([
            'is_latest' => false,
            'latest_sort_order' => 0,
        ]);

        return back()->with('success', $product->name . ' removed from Latest Deals.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:products,id'],
        ]);

        foreach ($validated['order'] as $index => $productId) {
            Product::whereKey($productId)->update([
                'is_latest' => true,
                'latest_sort_order' => $index + 1,
            ]);
        }

        return back()->with('success', 'Latest Deals order saved.');
    }
}
