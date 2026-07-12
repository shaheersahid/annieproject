@php
    $productUrl = route('product-detail', $product);
    $primaryImage = $product->primaryImage?->url ?? asset('assets/images/products/product-1.jpg');
    $hoverImage = $product->images?->firstWhere('type', 'gallery')?->url ?? $primaryImage;
    $reviewsCount = $product->reviews_count ?? 0;
    $ratingPercent = $product->affiliate_rating ? min(100, (float) $product->affiliate_rating * 20) : ($reviewsCount > 0 ? 80 : 0);
@endphp

<div class="product product-7 text-center">
    <figure class="product-media">
        @if($product->sale_price)
            <span class="product-label label-circle label-sale">Sale</span>
        @elseif($product->deal_enabled)
            <span class="product-label label-circle label-top">Deal</span>
        @endif

        <a href="{{ $productUrl }}">
            <img src="{{ $primaryImage }}" alt="{{ $product->name }}" class="product-image">
            <img src="{{ $hoverImage }}" alt="{{ $product->name }}" class="product-image-hover">
        </a>

        <div class="product-action">
            <a href="{{ $productUrl }}" class="btn-product btn-quickview"><span>View deal</span></a>
        </div>
    </figure>

    <div class="product-body">
        @if($product->categories->isNotEmpty())
            <div class="product-cat">
                <a href="{{ route('product-list', ['category' => $product->categories->first()->slug]) }}">{{ $product->categories->first()->name }}</a>
            </div>
        @endif

        <h3 class="product-title"><a href="{{ $productUrl }}">{{ $product->name }}</a></h3>
        <div class="product-price">
            @if($product->is_affiliate)
                {{ $product->price_note ?: 'Check latest price' }}
            @elseif($product->sale_price)
                <span class="new-price">{{ format_price($product->sale_price) }}</span>
                <span class="old-price">{{ format_price($product->base_price) }}</span>
            @else
                {{ format_price($product->base_price) }}
            @endif
        </div>
        <div class="ratings-container">
            <div class="ratings">
                <div class="ratings-val" style="width: {{ $ratingPercent }}%;"></div>
            </div>
            <span class="ratings-text">
                @if($product->affiliate_rating)
                    {{ number_format((float) $product->affiliate_rating, 1) }}/5
                @else
                    ( {{ $reviewsCount }} Reviews )
                @endif
            </span>
        </div>
        @if($product->is_affiliate)
            <div class="mt-1">
                @if($product->amazon_url)
                    <a href="{{ route('affiliate.redirect', [$product, 'amazon']) }}" class="btn btn-sm btn-outline-dark" target="_blank" rel="nofollow sponsored noopener">Amazon</a>
                @endif
                @if($product->temu_url)
                    <a href="{{ route('affiliate.redirect', [$product, 'temu']) }}" class="btn btn-sm btn-outline-dark" target="_blank" rel="nofollow sponsored noopener">Temu</a>
                @endif
            </div>
        @endif
    </div>
</div>
