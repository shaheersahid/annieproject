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
        $products = Product::query()
            ->with(['categories', 'images', 'primaryImage'])
            ->published()
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->whereHas('categories', function ($categoryQuery) use ($request): void {
                    $categoryQuery->where('slug', $request->string('category'));
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->with('children')
            ->withCount('products')
            ->active()
            ->parentCategories()
            ->ordered()
            ->get();

        return view('content.product-list', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        $product->load(['categories', 'images', 'primaryImage', 'reviews']);

        $relatedProducts = Product::query()
            ->with(['categories', 'images', 'primaryImage'])
            ->published()
            ->whereKeyNot($product->id)
            ->latest()
            ->take(8)
            ->get();

        return view('content.product-detail', compact('product', 'relatedProducts'));
    }
}
