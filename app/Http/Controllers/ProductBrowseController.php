<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductBrowseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) ($request->input('q') ?: $request->input('mobile-search')));

        $products = Product::query()
            ->withListing()
            ->published()
            ->search($search)
            ->forPlatform($request->input('platform'))
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->whereHas('categories', function ($categoryQuery) use ($request): void {
                    $categoryQuery->where('slug', $request->string('category'));
                });
            })
            ->when($request->input('sort') === 'popular', fn ($query) => $query->orderByDesc('click_count'))
            ->when($request->input('sort') === 'rating', fn ($query) => $query->orderByDesc('affiliate_rating'))
            ->when(! in_array($request->input('sort'), ['popular', 'rating'], true), fn ($query) => $query->latest())
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->with('children')
            ->withCount('products')
            ->active()
            ->parentCategories()
            ->ordered()
            ->get();

        return view('content.product-list', compact('products', 'categories', 'search'));
    }

    public function latest(): View
    {
        $products = Product::query()
            ->withListing()
            ->published()
            ->latestPicks()
            ->get();

        $reelProducts = $products->filter(fn (Product $product) => $product->isTiktokReel())->values();
        $otherProducts = $products->reject(fn (Product $product) => $product->isTiktokReel())->values();

        return view('content.latest-deals', compact('products', 'reelProducts', 'otherProducts'));
    }

    public function show(Product $product): View
    {
        $product->load(['categories', 'images', 'primaryImage', 'brand', 'tags', 'seo']);

        $relatedProducts = Product::query()
            ->withListing()
            ->published()
            ->whereKeyNot($product->id)
            ->latest()
            ->take(8)
            ->get();

        return view('content.product-detail', compact('product', 'relatedProducts'));
    }

    public function quickview(Product $product): View
    {
        $product->load(['categories', 'images', 'primaryImage', 'brand', 'tags']);

        return view('content.partials.product-quickview', compact('product'));
    }
}
