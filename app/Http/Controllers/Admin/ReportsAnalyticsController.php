<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class ReportsAnalyticsController extends Controller
{
    public function salesReports(): View
    {
        $orders = Order::with(['customer', 'items'])->latest()->get();
        $totalSales   = $orders->sum('grand_total');
        $totalOrders  = $orders->count();
        $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        $refunded     = $orders->where('status', 'refunded')->sum('grand_total');

        $chartRows = Order::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.content.reports-analytics.sales', compact(
            'orders', 'totalSales', 'totalOrders', 'averageOrder', 'refunded', 'chartRows'
        ));
    }

    public function sellerPerformance(): View
    {
        $sellers = Seller::withCount('products')->get();
        return view('admin.content.reports-analytics.seller-performance', compact('sellers'));
    }

    public function topProducts(): View
    {
        $products = Product::query()
            ->published()
            ->with(['categories', 'primaryImage', 'seller'])
            ->orderByDesc('click_count')
            ->take(20)
            ->get();

        return view('admin.content.reports-analytics.top-products', compact('products'));
    }

    public function categories(): View
    {
        $categories = Category::withCount('products')->orderByDesc('products_count')->get();
        return view('admin.content.reports-analytics.categories', compact('categories'));
    }
}
