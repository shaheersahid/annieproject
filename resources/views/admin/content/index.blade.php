@extends('admin.layouts.master')
@section('page-title', 'Affiliate Dashboard')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fs-16 fw-semibold mb-1 mb-md-2">
                            Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 18 ? 'Afternoon' : 'Evening') }},
                            <span class="text-primary">{{ auth()->user()->name }}!</span>
                        </h4>
                        <p class="text-muted mb-0">Affiliate performance overview for Amazon and Temu eyewear deals.</p>
                    </div>
                    <div class="page-title-right">
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex gap-2 align-items-center">
                            <div class="input-group">
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                                <span class="input-group-text">to</span>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light" title="Reset">
                                <i class="fas fa-undo me-1"></i> Reset
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            @foreach([
                ['label' => 'Affiliate Products', 'value' => $totalAffiliateProducts, 'class' => 'primary', 'icon' => 'fa-link'],
                ['label' => 'Published Deals', 'value' => $publishedDeals, 'class' => 'success', 'icon' => 'fa-check-circle'],
                ['label' => 'Total Clicks', 'value' => $totalAffiliateClicks, 'class' => 'info', 'icon' => 'fa-mouse-pointer'],
                ['label' => 'Featured Deals', 'value' => $featuredDeals, 'class' => 'warning', 'icon' => 'fa-star'],
            ] as $card)
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-{{ $card['class'] }}-subtle border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-1 small fw-semibold">{{ $card['label'] }}</p>
                                    <h4 class="mb-0 text-{{ $card['class'] }}">{{ $card['value'] }}</h4>
                                </div>
                                <div class="avatar avatar-lg">
                                    <i class="fas {{ $card['icon'] }} fs-32 text-{{ $card['class'] }} opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mb-4">
            @foreach([
                ['label' => 'Amazon Clicks', 'value' => $amazonClicks, 'class' => 'warning', 'icon' => 'fa-external-link-alt'],
                ['label' => 'Temu Clicks', 'value' => $temuClicks, 'class' => 'success', 'icon' => 'fa-external-link-alt'],
                ['label' => 'AliExpress Clicks', 'value' => $aliexpressClicks, 'class' => 'danger', 'icon' => 'fa-external-link-alt'],
                ['label' => 'Categories', 'value' => $totalCategories, 'class' => 'primary', 'icon' => 'fa-tags'],
            ] as $card)
                <div class="col-md-3 col-sm-6">
                    <div class="card bg-white border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-1 small fw-semibold">{{ $card['label'] }}</p>
                                    <h4 class="mb-0 text-{{ $card['class'] }}">{{ $card['value'] }}</h4>
                                </div>
                                <i class="fas {{ $card['icon'] }} fs-24 text-{{ $card['class'] }} opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mb-4">
            <div class="col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Platform Performance</h5>
                    </div>
                    <div class="card-body">
                        <div id="platformChart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h5 class="card-title mb-0">Top Clicked Products</h5>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary">Manage Products</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Platform</th>
                                        <th class="text-end">Clicks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topClickedProducts as $product)
                                        <tr>
                                            <td class="fw-semibold text-truncate">{{ $product->name }}</td>
                                            <td>{{ str($product->affiliate_platform)->headline() }}</td>
                                            <td class="text-end"><span class="badge bg-primary">{{ $product->click_count }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No affiliate clicks yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Recent Affiliate Clicks</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Platform</th>
                                        <th>Referrer</th>
                                        <th class="text-end">Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAffiliateClicks as $click)
                                        <tr>
                                            <td>{{ $click->product?->name ?? 'Deleted product' }}</td>
                                            <td>
                                                @php $pClass = match($click->platform) { 'amazon' => 'warning', 'temu' => 'success', 'aliexpress' => 'danger', default => 'secondary' }; @endphp
                                                <span class="badge bg-{{ $pClass }}">{{ ucfirst($click->platform) }}</span>
                                            </td>
                                            <td class="text-truncate" style="max-width: 260px;">{{ $click->referrer ?: 'Direct' }}</td>
                                            <td class="text-end">{{ $click->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No clicks in this period</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary text-start">
                            <i class="fas fa-plus me-2"></i> Add Affiliate Product
                        </a>
                        <a href="{{ route('product-list') }}" class="btn btn-outline-primary text-start" target="_blank">
                            <i class="fas fa-eye me-2"></i> View Deals Page
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary text-start">
                            <i class="fas fa-tags me-2"></i> Manage Categories
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary text-start" target="_blank">
                            <i class="fas fa-globe me-2"></i> Open Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>
<script>
    const platformOptions = {
        chart: { type: 'donut', height: 300 },
        labels: @json($platformPerformance['labels']),
        series: @json($platformPerformance['series']),
        colors: ['#f1b44c', '#28a745', '#e83e3e'],
        legend: { position: 'bottom' },
        dataLabels: { enabled: true },
        noData: { text: 'No clicks in this period' }
    };

    new ApexCharts(document.querySelector('#platformChart'), platformOptions).render();
</script>
@endpush
