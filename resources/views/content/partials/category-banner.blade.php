@php
    $image = $category->images?->url ?? asset($fallbackImage);
    $url = route('product-list', ['category' => $category->slug]);
@endphp

<div class="{{ $columnClass ?? 'col-md-6 col-lg-4' }}">
    <div class="banner banner-overlay {{ $textClass ?? 'text-white' }}">
        <a href="{{ $url }}">
            <img src="{{ $image }}" alt="{{ $category->name }}">
        </a>

        <div class="banner-content {{ $contentClass ?? '' }}">
            <h4 class="banner-subtitle"><a href="{{ $url }}">{{ $category->products_count ?? $category->products_count ?? 0 }} Products</a></h4>
            <h3 class="banner-title"><a href="{{ $url }}">{{ $category->name }}</a></h3>
            <a href="{{ $url }}" class="btn underline {{ $buttonClass ?? 'btn-outline-white-3' }} banner-link">View Deals</a>
        </div>
    </div>
</div>
