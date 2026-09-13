@extends('layouts.main')

@section('title', 'Latest Deals - Smart Comfort Deals')
@section('meta-description', 'Latest promoted comfort deals, with TikTok and Reel picks shown first.')

@section('content')
<main class="main">
    <div class="page-header text-center" style="background-image: url('{{ asset('assets/images/page-header-bg.jpg') }}')">
        <div class="container">
            <h1 class="page-title">Latest Deals<span>Promoted picks, TikTok & Reels first</span></h1>
        </div>
    </div>

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Latest Deals</li>
            </ol>
        </div>
    </nav>

    <div class="page-content">
        <div class="container">
            <div class="alert alert-info">
                Affiliate disclosure: Smart Comfort Deals may earn a commission when you buy through Amazon, Temu or AliExpress links. Prices and availability can change, so always confirm on the retailer site.
            </div>

            @if($products->isEmpty())
                <p class="text-muted text-center py-5">No latest deals have been selected yet.</p>
            @else
                @if($reelProducts->isNotEmpty())
                    <div class="heading heading-center mb-3">
                        <h2 class="title">From TikTok &amp; Reels</h2>
                    </div>
                    <div class="products mb-5">
                        <div class="row justify-content-center">
                            @foreach($reelProducts as $product)
                                <div class="col-6 col-md-4 col-lg-3">
                                    @include('content.partials.product-card', ['product' => $product, 'linkToProduct' => true])
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($otherProducts->isNotEmpty())
                    <div class="heading heading-center mb-3">
                        <h2 class="title">More Promoted Picks</h2>
                    </div>
                    <div class="products mb-3">
                        <div class="row justify-content-center">
                            @foreach($otherProducts as $product)
                                <div class="col-6 col-md-4 col-lg-3">
                                    @include('content.partials.product-card', ['product' => $product, 'linkToProduct' => true])
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</main>
@endsection
