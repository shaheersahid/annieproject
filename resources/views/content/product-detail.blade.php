@extends('layouts.main')

@section('title', $product->name)

@section('content')
@php
    $primaryImage = $product->primaryImage?->url ?? asset('assets/images/products/product-1.jpg');
    $gallery = $product->images->where('type', 'gallery')->values();
    $ratingPercent = $product->affiliate_rating ? min(100, (float) $product->affiliate_rating * 20) : 0;
@endphp

<main class="main product-detail-page">
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
            <div class="alert alert-info product-detail-disclosure">
                Affiliate disclosure: Annie Eyewear may earn a commission from qualifying purchases through Amazon or Temu links. Retailer prices and availability apply at checkout.
            </div>

            <div class="product-details-top">
                <div class="row product-detail-grid">
                    <div class="col-lg-6">
                        <div class="product-gallery product-gallery-vertical product-detail-gallery">
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

                    <div class="col-lg-6">
                        <div class="product-details product-detail-summary">
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

                            <div class="product-details-action product-detail-actions">
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

@push('page-styles')
<style>
    .product-detail-page .page-content {
        padding-top: 2rem;
        padding-bottom: 5rem;
    }

    .product-detail-disclosure {
        border: 0;
        border-left: .4rem solid #0097b2;
        margin-bottom: 2.4rem;
        color: #4f5f66;
        background: #eef9fb;
    }

    .product-detail-grid {
        align-items: flex-start;
    }

    .product-detail-gallery {
        display: flex;
        gap: 1.2rem;
        margin-bottom: 2.5rem;
    }

    .product-detail-gallery .product-main-image {
        flex: 1 1 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 44rem;
        margin-bottom: 0;
        background: #fff;
        border: .1rem solid #ebebeb;
    }

    .product-detail-gallery .product-main-image img {
        display: block;
        width: 100%;
        max-height: 54rem;
        object-fit: contain;
    }

    .product-detail-gallery .product-image-gallery {
        flex: 0 0 8.8rem;
        width: 8.8rem;
        margin: 0;
        order: -1;
    }

    .product-detail-gallery .product-gallery-item {
        width: 8.8rem;
        height: 8.8rem;
        margin: 0 0 1rem;
        border: .1rem solid #ebebeb;
        background: #fff;
    }

    .product-detail-gallery .product-gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .product-detail-summary {
        padding: 3rem;
        background: #fff;
        border: .1rem solid #ebebeb;
    }

    .product-detail-summary .product-title {
        margin-bottom: 1.2rem;
        font-size: 2.8rem;
        line-height: 1.25;
        letter-spacing: 0;
    }

    .product-detail-summary .product-price {
        margin-bottom: 1.8rem;
        font-size: 2.4rem;
        font-weight: 600;
        color: #222;
    }

    .product-detail-summary .product-content {
        margin-bottom: 2rem;
        color: #555;
    }

    .product-detail-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        margin-bottom: 2rem;
    }

    .product-detail-actions .btn-product {
        min-width: 17rem;
        margin: 0;
        padding: 1.15rem 1.8rem;
        justify-content: center;
        border: .1rem solid #0097b2;
        background: #0097b2;
    }

    .product-detail-actions .btn-product span {
        color: #fff;
    }

    .product-detail-page .product-details-footer {
        display: block;
        padding-top: 1.6rem;
        border-top: .1rem solid #ebebeb;
    }

    .product-detail-page .product-details-tab {
        margin-top: 3rem;
    }

    .product-detail-page .product-desc-content {
        max-width: 100%;
        padding: 2.4rem;
        background: #fff;
        border: .1rem solid #ebebeb;
    }

    @media screen and (max-width: 991px) {
        .product-detail-summary {
            padding: 2.2rem;
        }

        .product-detail-gallery .product-main-image {
            min-height: 34rem;
        }
    }

    @media screen and (max-width: 767px) {
        .product-detail-gallery {
            display: block;
        }

        .product-detail-gallery .product-main-image {
            min-height: 28rem;
            margin-bottom: 1.2rem;
        }

        .product-detail-gallery .product-image-gallery {
            display: flex;
            width: 100%;
            gap: .8rem;
            overflow-x: auto;
        }

        .product-detail-gallery .product-gallery-item {
            flex: 0 0 7.4rem;
            width: 7.4rem;
            height: 7.4rem;
            margin: 0;
        }

        .product-detail-summary {
            padding: 1.8rem;
        }

        .product-detail-summary .product-title {
            font-size: 2.2rem;
        }

        .product-detail-summary .product-price {
            font-size: 2rem;
        }

        .product-detail-actions .btn-product {
            width: 100%;
        }

        .product-detail-page .product-desc-content {
            padding: 1.8rem;
        }
    }
</style>
@endpush
