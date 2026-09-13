@extends('layouts.main')
@section('title', 'Smart Comfort Deals - Top Ergonomic, Home & Office Support Deals')

@php
    $bannerFallbacks = [
        'assets/images/demos/demo-7/banners/banner-1.jpg',
        'assets/images/demos/demo-7/banners/banner-2.jpg',
        'assets/images/demos/demo-7/banners/banner-3.jpg',
        'assets/images/demos/demo-7/banners/banner-4.jpg',
        'assets/images/demos/demo-7/banners/banner-5.jpg',
    ];
    $heroCategories = $homeCategories->take(5)->values();
    $lcpImage = $heroCategories->first()?->images?->url
        ?? asset($bannerFallbacks[0]);
@endphp

@push('seo-head')
    <link rel="preload" as="image" href="{{ $lcpImage }}" fetchpriority="high">
@endpush

@section('content')
<main class="main">
    <div class="page-header homepage-page-header text-center">
        <div class="container">
            <h1 class="page-title mb-1">
                Smart Comfort Deals
                <span>Home, office &amp; everyday comfort picks</span>
            </h1>
            <p class="homepage-hero-lead mb-0">
                Ergonomic accessories and practical finds from Amazon, Temu &amp; AliExpress.
            </p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            @foreach($heroCategories->take(2) as $index => $category)
                @include('content.partials.category-banner', [
                    'category' => $category,
                    'fallbackImage' => $bannerFallbacks[$index] ?? $bannerFallbacks[0],
                    'columnClass' => 'col-lg-6',
                    'contentClass' => 'banner-content-center',
                    'buttonClass' => '',
                    'priority' => $index === 0,
                ])
            @endforeach
        </div>

        <div class="row justify-content-center">
            @foreach($heroCategories->slice(2, 3) as $index => $category)
                @include('content.partials.category-banner', [
                    'category' => $category,
                    'fallbackImage' => $bannerFallbacks[$index + 2] ?? $bannerFallbacks[2],
                    'columnClass' => 'col-md-6 col-lg-4',
                    'contentClass' => '',
                    'textClass' => $loop->even ? 'color-grey' : 'text-white',
                    'buttonClass' => $loop->even ? '' : 'btn-outline-white-3',
                    'priority' => false,
                ])
            @endforeach
        </div>
    </div>

    <div class="icon-boxes-container bg-transparent">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-12 icon-boxes">
                    <div class="col-sm-6 col-lg-4">
                        <div class="icon-box icon-box-side">
                            <span class="icon-box-icon"><i class="icon-truck"></i></span>
                            <div class="icon-box-content">
                                <div class="icon-box-title">Amazon & Temu Picks</div>
                                <p>Compare comfort & ergonomic deals before you buy</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="icon-box icon-box-side">
                            <span class="icon-box-icon"><i class="icon-rotate-left"></i></span>
                            <div class="icon-box-content">
                                <div class="icon-box-title">Latest Price Check</div>
                                <p>Retailer price and availability apply</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="icon-box icon-box-side">
                            <span class="icon-box-icon"><i class="icon-headphones"></i></span>
                            <div class="icon-box-content">
                                <div class="icon-box-title">Clear Disclosure</div>
                                <p>We may earn from affiliate links</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-light-2 pt-6 pb-6 featured">
        <div class="container-fluid">
            <div class="heading heading-center mb-3">
                <h2 class="title">Featured Comfort Deals</h2>

                @if($featuredCategories->isNotEmpty())
                    <ul class="nav nav-pills justify-content-center" role="tablist">
                        @foreach($featuredCategories as $category)
                            <li class="nav-item">
                                <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="featured-{{ $category->id }}-link" data-toggle="tab" href="#featured-{{ $category->id }}-tab" role="tab" aria-controls="featured-{{ $category->id }}-tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $category->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="tab-content tab-content-carousel">
                @forelse($featuredCategories as $category)
                    @php $tabProducts = $categoryProducts->get($category->id, collect()); @endphp
                    <div class="tab-pane p-0 fade {{ $loop->first ? 'show active' : '' }}" id="featured-{{ $category->id }}-tab" role="tabpanel" aria-labelledby="featured-{{ $category->id }}-link">
                        @if($tabProducts->isEmpty())
                            <p class="text-muted text-center py-4 mb-0">No featured deals selected for {{ $category->name }} yet.</p>
                        @else
                            <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl"
                                data-owl-options='{"nav": false, "dots": true, "margin": 20, "loop": false, "responsive": {"0": {"items":2}, "480": {"items":2}, "768": {"items":3}, "992": {"items":4}, "1200": {"items":5, "nav": true}}}'>
                                @foreach($tabProducts as $product)
                                    @include('content.partials.product-card', ['product' => $product, 'linkToProduct' => true])
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="tab-pane p-0 fade show active">
                        <p class="text-muted text-center py-4">No featured categories available yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="container-fluid new-arrivals">
        <div class="heading heading-center mb-3">
            <h2 class="title">Latest Promoted Picks</h2>
        </div>

        <div class="products">
            <div class="row justify-content-center">
                @forelse($newProducts as $product)
                    <div class="col-6 col-md-4 col-lg-3 col-xl-5col">
                        @include('content.partials.product-card', ['product' => $product])
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">No latest deals selected yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="more-container text-center mt-2">
            <a href="{{ route('latest-deals') }}" class="btn btn-outline-dark-3 btn-more"><span>View latest deals</span><i class="icon-long-arrow-right"></i></a>
        </div>

        <hr class="mt-0 mb-6">

        <div class="blog-posts mb-4">
            <h2 class="title text-center mb-3">Shop by Category</h2>

            <div class="owl-carousel owl-simple mb-2" data-toggle="owl"
                data-owl-options='{"nav": false, "dots": true, "items": 3, "margin": 20, "loop": false, "responsive": {"0": {"items":1}, "520": {"items":2}, "768": {"items":3}, "992": {"items":4}}}'>
                @foreach($homeCategories as $index => $category)
                    @php
                        $image = $category->images?->url ?? asset($bannerFallbacks[$index % count($bannerFallbacks)]);
                    @endphp
                    <article class="entry">
                        <figure class="entry-media">
                            <a href="{{ route('product-list', ['category' => $category->slug]) }}">
                                <img src="{{ $image }}" alt="{{ seo_alt($category->name . ' comfort deals from Smart Comfort Deals') }}" width="640" height="420" loading="lazy" decoding="async">
                            </a>
                        </figure>

                        <div class="entry-body text-center">
                            <div class="entry-meta">
                                <a href="{{ route('product-list', ['category' => $category->slug]) }}">{{ $category->products_count }} Products</a>
                            </div>

                            <h3 class="entry-title">
                                <a href="{{ route('product-list', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                            </h3>

                            <div class="entry-content">
                                <a href="{{ route('product-list', ['category' => $category->slug]) }}" class="read-more">View Deals</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>

</main>
@endsection
