<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Contracts\View\View;

class ReportsAnalyticsController extends Controller
{
    public function salesReports(): View
    {
        return view('admin.content.reports-analytics.sales');
    }

    public function sellerPerformance(): View
    {
        $sellers = Seller::withCount('products')->get();
        return view('admin.content.reports-analytics.seller-performance', compact('sellers'));
    }

    public function topProducts(): View
    {
        $topProducts = Product::query()
            ->published()
            ->orderByDesc('click_count')
            ->take(20)
            ->get();

        return view('admin.content.reports-analytics.top-products', compact('topProducts'));
    }

    public function categories(): View
    {
        $categories = Category::withCount('products')->get();
        return view('admin.content.reports-analytics.top-products', compact('categories'));
    }
}
