@php
    $productUrl = route('product-detail', $product);
    $linkToProduct = $linkToProduct ?? true;
    $actionUrl = $linkToProduct ? $productUrl : route('product-quickview', $product);
    $actionClass = $linkToProduct ? 'btn-product btn-cart' : 'btn-product btn-quickview';
    $primaryImage = $product->primaryImage?->url ?? asset('assets/images/products/product-1.jpg');
    $hoverImage = $product->images?->firstWhere('type', 'gallery')?->url ?? $primaryImage;
    $enabledTags = $product->enabledTags();
    $isReel = $product->isTiktokReel();
@endphp

<div class="product product-7 text-center">
    <figure class="product-media">
        @if($isReel)
            <span class="product-label label-circle label-new">Reel</span>
        @elseif($product->sale_price)
            <span class="product-label label-circle label-sale">Sale</span>
        @elseif($product->deal_enabled)
            <span class="product-label label-circle label-top">Deal</span>
        @elseif($product->is_latest)
            <span class="product-label label-circle label-new">Latest</span>
        @endif

        <a href="{{ $productUrl }}">
            <img src="{{ $primaryImage }}" alt="{{ product_image_alt($product, 'product photo') }}" class="product-image">
            <img src="{{ $hoverImage }}" alt="{{ product_image_alt($product, 'alternate view') }}" class="product-image-hover">
        </a>

        <div class="product-action">
            <a href="{{ $actionUrl }}" class="{{ $actionClass }}"><span>View deal</span></a>
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
            @elseif($product->sale_price && (float) $product->sale_price > 0)
                <span class="new-price">{{ format_price($product->sale_price) }}</span>
                @if((float) $product->base_price > 0)
                    <span class="old-price">{{ format_price($product->base_price) }}</span>
                @endif
            @elseif((float) $product->base_price > 0)
                {{ format_price($product->base_price) }}
            @elseif($product->price_note)
                {{ $product->price_note }}
            @else
                Check latest price
            @endif
        </div>
        @if($isReel)
            <div class="product-feature-list">
                <span class="product-feature-badge">TikTok / Reel</span>
            </div>
        @endif
        @if($enabledTags->isNotEmpty())
            <div class="product-feature-list">
                @foreach($enabledTags as $tag)
                    <span class="product-feature-badge">{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif
        @if($product->is_affiliate)
            <div class="mt-1">
                @if($product->amazon_url)
                    <a href="{{ route('affiliate.redirect', [$product, 'amazon']) }}" class="btn btn-sm btn-outline-dark" target="_blank" rel="nofollow sponsored noopener">Amazon</a>
                @endif
                @if($product->temu_url)
                    <a href="{{ route('affiliate.redirect', [$product, 'temu']) }}" class="btn btn-sm btn-outline-dark" target="_blank" rel="nofollow sponsored noopener">Temu</a>
                @endif
                @if($product->aliexpress_url)
                    <a href="{{ route('affiliate.redirect', [$product, 'aliexpress']) }}" class="btn btn-sm btn-outline-dark" target="_blank" rel="nofollow sponsored noopener">AliExpress</a>
                @endif
            </div>
        @endif
    </div>
</div>
