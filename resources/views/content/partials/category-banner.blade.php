@php
    $image = $category->images?->url ?? asset($fallbackImage);
    $url = route('product-list', ['category' => $category->slug]);
    $priority = $priority ?? false;
@endphp

<div class="{{ $columnClass ?? 'col-md-6 col-lg-4' }}">
    <div class="banner banner-overlay {{ $textClass ?? 'text-white' }}">
        <a href="{{ $url }}">
            <img
                src="{{ $image }}"
                alt="{{ seo_alt($category->name . ' comfort deals from Smart Comfort Deals') }}"
                width="800"
                height="450"
                decoding="async"
                @if($priority)
                    fetchpriority="high"
                    loading="eager"
                @else
                    loading="lazy"
                @endif
            >
        </a>

        <div class="banner-content {{ $contentClass ?? '' }}">
            <div class="banner-subtitle"><a href="{{ $url }}">{{ $category->products_count ?? 0 }} Products</a></div>
            <div class="banner-title"><a href="{{ $url }}">{{ $category->name }}</a></div>
            <a href="{{ $url }}" class="btn underline {{ $buttonClass ?? 'btn-outline-white-3' }} banner-link">View Deals</a>
        </div>
    </div>
</div>
