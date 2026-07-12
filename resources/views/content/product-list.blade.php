@extends('layouts.main')

@php
    $activeCategoryName = null;
    if (request('category')) {
        $activeCategory = \App\Models\Category::where('slug', request('category'))->first();
        if ($activeCategory) {
            $activeCategoryName = $activeCategory->name;
        }
    }
@endphp

@section('title', $activeCategoryName ? $activeCategoryName . ' - Smart Comfort Deals' : 'Smart Comfort Deals')

@section('content')
<main class="main">
    <div class="page-header text-center" style="background-image: url('{{ asset('assets/images/page-header-bg.jpg') }}')">
        <div class="container">
            @if($activeCategoryName)
                <h1 class="page-title">{{ $activeCategoryName }}<span>Smart Comfort Deals</span></h1>
            @else
                <h1 class="page-title">Smart Comfort Deals<span>Top ergonomic, home & office picks</span></h1>
            @endif
        </div>
    </div>

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                @if($activeCategoryName)
                    <li class="breadcrumb-item"><a href="{{ route('product-list') }}">Deals</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $activeCategoryName }}</li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">All Deals</li>
                @endif
            </ol>
        </div>
    </nav>

    <div class="page-content">
        <div class="container">
            <div class="alert alert-info">
                Affiliate disclosure: Smart Comfort Deals may earn a commission when you buy through Amazon, Temu or AliExpress links. Prices and availability can change, so always confirm on the retailer site.
            </div>

            <form method="GET" action="{{ route('product-list') }}" class="toolbox mb-3">
                <div class="toolbox-left">
                    <div class="toolbox-info">
                        Showing <span>{{ $products->count() }} of {{ $products->total() }}</span> deals
                    </div>
                </div>
                <div class="toolbox-right">
                    <div class="toolbox-sort">
                        <label for="platform">Platform:</label>
                        <div class="select-custom">
                            <select name="platform" id="platform" class="form-control" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="amazon" @selected(request('platform') === 'amazon')>Amazon</option>
                                <option value="temu" @selected(request('platform') === 'temu')>Temu</option>
                            </select>
                        </div>
                    </div>
                    <div class="toolbox-sort">
                        <label for="sort">Sort by:</label>
                        <div class="select-custom">
                            <select name="sort" id="sort" class="form-control" onchange="this.form.submit()">
                                <option value="">Newest</option>
                                <option value="popular" @selected(request('sort') === 'popular')>Most Clicked</option>
                                <option value="rating" @selected(request('sort') === 'rating')>Rating</option>
                            </select>
                        </div>
                    </div>
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                </div>
            </form>

            <div class="row">
                <div class="col-lg-9">
                    <div class="products mb-3">
                        <div class="row justify-content-center">
                            @forelse($products as $product)
                                <div class="col-6 col-md-4 col-xl-3">
                                    @include('content.partials.product-card', ['product' => $product])
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted text-center py-5">No affiliate deals found.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{ $products->links() }}
                </div>

                <aside class="col-lg-3 order-lg-first">
                    <div class="sidebar sidebar-shop">
                        <div class="widget widget-clean">
                            <label>Filters:</label>
                            <a href="{{ route('product-list') }}">Clean All</a>
                        </div>

                        <div class="widget widget-collapsible">
                            <h3 class="widget-title">
                                <a data-toggle="collapse" href="#widget-category" role="button" aria-expanded="true" aria-controls="widget-category">Category</a>
                            </h3>
                            <div class="collapse show" id="widget-category">
                                <div class="widget-body">
                                    <div class="filter-items filter-items-count">
                                        @foreach($categories as $category)
                                            <div class="filter-item">
                                                <a class="{{ request('category') === $category->slug ? 'font-weight-bold' : '' }}" href="{{ route('product-list', array_filter(['category' => $category->slug, 'platform' => request('platform'), 'sort' => request('sort')])) }}">
                                                    {{ $category->name }}
                                                </a>
                                                <span class="item-count">{{ $category->products_count }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</main>
@endsection
