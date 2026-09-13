@php
    $primaryImage = $product->primaryImage?->url ?? asset('assets/images/products/product-1.jpg');
    $gallery = $product->images->where('type', 'gallery')->values();
    $slides = collect([$product->primaryImage])->filter()->concat($gallery)->unique('id')->values();
    if ($slides->isEmpty()) {
        $slides = collect([(object) ['url' => $primaryImage]]);
    }
    $enabledTags = $product->enabledTags();
    $enabledAttributes = $product->enabledAttributes();
@endphp

<div class="container quickView-container">
    <div class="quickView-content">
        <div class="row">
            <div class="col-lg-7 col-md-6">
                <div class="row">
                    @if($slides->count() > 1)
                        <div class="product-left">
                            @foreach($slides as $index => $image)
                                <a href="#qv-{{ $product->id }}-{{ $index }}" class="carousel-dot {{ $loop->first ? 'active' : '' }}">
                                    <img src="{{ $image->url ?? $primaryImage }}" alt="{{ product_image_alt($product, 'thumbnail ' . ($index + 1)) }}">
                                </a>
                            @endforeach
                        </div>
                    @endif
                    <div class="product-right">
                        <div class="owl-carousel owl-theme owl-nav-inside owl-light mb-0" data-toggle="owl" data-owl-options='{"dots": false, "nav": false, "URLhashListener": true, "responsive": {"900": {"nav": true, "dots": true}}}'>
                            @foreach($slides as $index => $image)
                                <div class="intro-slide" data-hash="qv-{{ $product->id }}-{{ $index }}">
                                    <img src="{{ $image->url ?? $primaryImage }}" alt="{{ product_image_alt($product, 'product photo ' . ($index + 1)) }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-6">
                <h2 class="product-title">{{ $product->name }}</h2>
                <h3 class="product-price">
                    @if($product->is_affiliate)
                        {{ $product->price_note ?: 'Check latest price' }}
                    @elseif($product->sale_price && (float) $product->sale_price > 0)
                        {{ format_price($product->sale_price) }}
                    @elseif((float) $product->base_price > 0)
                        {{ format_price($product->base_price) }}
                    @else
                        Check latest price
                    @endif
                </h3>

                <p class="product-txt">{!! $product->short_description ?: 'Selected product deal from Amazon, Temu or AliExpress.' !!}</p>

                @if($enabledTags->isNotEmpty() || $enabledAttributes->isNotEmpty())
                    <div class="product-feature-list mb-2">
                        @foreach($enabledTags as $tag)
                            <span class="product-feature-badge">{{ $tag->name }}</span>
                        @endforeach
                        @foreach($enabledAttributes as $attribute)
                            <span class="product-feature-badge">{{ $attribute->name }}</span>
                        @endforeach
                    </div>
                @endif

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
                        <a href="{{ route('product-detail', $product) }}" class="btn-product btn-cart"><span>View deal</span></a>
                    @endunless
                </div>

                <div class="product-details-footer">
                    <div class="product-cat">
                        <span>Category:</span>
                        @foreach($product->categories as $category)
                            <a href="{{ route('product-list', ['category' => $category->slug]) }}">{{ $category->name }}</a>@if(! $loop->last), @endif
                        @endforeach
                    </div>
                    <a href="{{ route('product-detail', $product) }}" class="btn btn-link p-0">See full deal details</a>
                </div>
            </div>
        </div>
    </div>
</div>
