<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductBrowseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) ($request->input('q') ?: $request->input('mobile-search')));

        $activeCategory = null;
        if ($request->filled('category')) {
            $activeCategory = Category::query()
                ->with('seo')
                ->active()
                ->where('slug', $request->string('category'))
                ->first();
        }

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

        return view('content.product-list', compact('products', 'categories', 'search', 'activeCategory'));
    }

    public function suggest(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('q', ''));

        if (mb_strlen($search) < 2) {
            return response()->json(['products' => []]);
        }

        $products = Product::query()
            ->withListing()
            ->published()
            ->search($search)
            ->orderByDesc('is_featured')
            ->orderByDesc('click_count')
            ->orderByDesc('updated_at')
            ->take(8)
            ->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'url' => route('product-detail', $product),
                    'image' => $product->primaryImage?->url
                        ?? asset('assets/images/products/product-1.jpg'),
                    'category' => $product->categories->first()?->name,
                    'price' => $product->is_affiliate
                        ? ($product->price_note ?: 'Check latest price')
                        : (
                            $product->sale_price && (float) $product->sale_price > 0
                                ? format_price($product->sale_price)
                                : (
                                    (float) $product->base_price > 0
                                        ? format_price($product->base_price)
                                        : ($product->price_note ?: 'Check latest price')
                                )
                        ),
                ];
            })
            ->values();

        return response()->json([
            'products' => $products,
            'view_all_url' => route('product-list', ['q' => $search]),
        ]);
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

        $relatedProducts = $product->relatedProducts(8);

        return view('content.product-detail', compact('product', 'relatedProducts'));
    }

    public function quickview(Product $product): View
    {
        $product->load(['categories', 'images', 'primaryImage', 'brand', 'tags']);

        return view('content.partials.product-quickview', compact('product'));
    }
}
