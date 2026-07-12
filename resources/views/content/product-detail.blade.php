@extends('layouts.main')

@section('title', $product->name)

@section('content')
@php
    $primaryImage = $product->primaryImage?->url ?? asset('assets/images/products/product-1.jpg');
    $gallery = $product->images->where('type', 'gallery')->values();
    $ratingPercent = $product->affiliate_rating ? min(100, (float) $product->affiliate_rating * 20) : 0;
@endphp

<main class="main">
    <nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-0">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('product-list') }}">Deals</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </div>
    </nav>

    <div class="page-content">
        <div class="container">
            <div class="alert alert-info">
                Affiliate disclosure: Annie Eyewear may earn a commission from qualifying purchases through Amazon or Temu links. Retailer prices and availability apply at checkout.
            </div>

            <div class="product-details-top">
                <div class="row">
                    <div class="col-md-6">
                        <div class="product-gallery product-gallery-vertical">
                            <figure class="product-main-image">
                                <img id="product-zoom" src="{{ $primaryImage }}" alt="{{ $product->name }}">
                            </figure>

                            @if($gallery->isNotEmpty())
                                <div id="product-zoom-gallery" class="product-image-gallery">
                                    @foreach($gallery as $image)
                                        <a class="product-gallery-item" href="#">
                                            <img src="{{ $image->url }}" alt="{{ $product->name }}">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="product-details">
                            <h1 class="product-title">{{ $product->name }}</h1>

                            @if($product->affiliate_rating)
                                <div class="ratings-container">
                                    <div class="ratings">
                                        <div class="ratings-val" style="width: {{ $ratingPercent }}%;"></div>
                                    </div>
                                    <span class="ratings-text">{{ number_format((float) $product->affiliate_rating, 1) }}/5 editorial rating</span>
                                </div>
                            @endif

                            <div class="product-price">
                                {{ $product->is_affiliate ? ($product->price_note ?: 'Check latest price') : format_price($product->sale_price ?: $product->base_price) }}
                            </div>

                            <div class="product-content">
                                {!! $product->short_description ?: '<p>Selected eyewear deal from Amazon or Temu.</p>' !!}
                            </div>

                            <div class="product-details-action">
                                @if($product->amazon_url)
                                    <a href="{{ route('affiliate.redirect', [$product, 'amazon']) }}" class="btn-product btn-cart" target="_blank" rel="nofollow sponsored noopener"><span>Buy on Amazon</span></a>
                                @endif
                                @if($product->temu_url)
                                    <a href="{{ route('affiliate.redirect', [$product, 'temu']) }}" class="btn-product btn-cart" target="_blank" rel="nofollow sponsored noopener"><span>Buy on Temu</span></a>
                                @endif
                                @if($product->aliexpress_url)
                                    <a href="{{ route('affiliate.redirect', [$product, 'aliexpress']) }}" class="btn-product btn-cart" target="_blank" rel="nofollow sponsored noopener"><span>Buy on AliExpress</span></a>
                                @endif
                                @unless($product->amazon_url || $product->temu_url || $product->aliexpress_url)
                                    <a href="{{ route('contact') }}" class="btn-product btn-cart"><span>Contact for availability</span></a>
                                @endunless
                            </div>

                            <div class="product-details-footer">
                                <div class="product-cat">
                                    <span>Category:</span>
                                    @foreach($product->categories as $category)
                                        <a href="{{ route('product-list', ['category' => $category->slug]) }}">{{ $category->name }}</a>@if(! $loop->last), @endif
                                    @endforeach
                                </div>
                                @if($product->brand)
                                    <div class="product-cat"><span>Brand:</span> {{ $product->brand->name }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-details-tab">
                <ul class="nav nav-pills justify-content-center" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="product-desc-link" data-toggle="tab" href="#product-desc-tab" role="tab">Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="product-info-link" data-toggle="tab" href="#product-info-tab" role="tab">Specs</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel">
                        <div class="product-desc-content">
                            <h3>Product Information</h3>
                            {!! $product->description ?: '<p>This affiliate pick is listed so shoppers can compare eyewear options before buying from the retailer.</p>' !!}

                            <div class="row mt-3">
                                @if(!empty($product->pros))
                                    <div class="col-md-6">
                                        <h3>Pros</h3>
                                        <ul>
                                            @foreach($product->pros as $pro)
                                                <li>{{ $pro }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if(!empty($product->cons))
                                    <div class="col-md-6">
                                        <h3>Cons</h3>
                                        <ul>
                                            @foreach($product->cons as $con)
                                                <li>{{ $con }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="product-info-tab" role="tabpanel">
                        <div class="product-desc-content">
                            <h3>Additional Information</h3>
                            @php($items = data_get($product->specifications, 'items', []))
                            @if(!empty($items))
                                <table class="table">
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <th>{{ $item['key'] ?? '' }}</th>
                                                <td>{{ $item['value'] ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No additional specifications have been added yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($relatedProducts->isNotEmpty())
                <h2 class="title text-center mb-4">You May Also Like</h2>
                <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl"
                    data-owl-options='{"nav": false, "dots": true, "margin": 20, "loop": false, "responsive": {"0": {"items":1}, "480": {"items":2}, "768": {"items":3}, "992": {"items":4}, "1200": {"items":4, "nav": true, "dots": false}}}'>
                    @foreach($relatedProducts as $relatedProduct)
                        @include('content.partials.product-card', ['product' => $relatedProduct])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</main>
@endsection
