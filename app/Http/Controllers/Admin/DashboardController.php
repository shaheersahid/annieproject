<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateClick;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        $clicksQuery = AffiliateClick::query()
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);

        $totalAffiliateProducts = Product::query()
            ->whereNotIn('affiliate_platform', ['none'])
            ->whereNotNull('affiliate_platform')
            ->count();

        $publishedDeals = Product::query()
            ->published()
            ->whereNotIn('affiliate_platform', ['none'])
            ->count();

        $totalAffiliateClicks = (clone $clicksQuery)->count();
        $featuredDeals = Product::query()->where('is_featured', true)->count();
        $amazonClicks  = (clone $clicksQuery)->where('platform', 'amazon')->count();
        $temuClicks    = (clone $clicksQuery)->where('platform', 'temu')->count();
        $aliexpressClicks = (clone $clicksQuery)->where('platform', 'aliexpress')->count();
        $draftProducts = Product::query()->draft()->count();
        $totalCategories = Category::query()->count();

        $topClickedProducts = Product::query()
            ->where('click_count', '>', 0)
            ->whereNotIn('affiliate_platform', ['none'])
            ->orderByDesc('click_count')
            ->take(10)
            ->get();

        $recentAffiliateClicks = (clone $clicksQuery)
            ->with('product')
            ->latest()
            ->take(20)
            ->get();

        $platformPerformance = [
            'labels' => ['Amazon', 'Temu', 'AliExpress'],
            'series' => [$amazonClicks, $temuClicks, $aliexpressClicks],
        ];

        return view('admin.content.index', compact(
            'startDate',
            'endDate',
            'totalAffiliateProducts',
            'publishedDeals',
            'totalAffiliateClicks',
            'featuredDeals',
            'amazonClicks',
            'temuClicks',
            'aliexpressClicks',
            'draftProducts',
            'totalCategories',
            'topClickedProducts',
            'recentAffiliateClicks',
            'platformPerformance',
        ));
    }
}
