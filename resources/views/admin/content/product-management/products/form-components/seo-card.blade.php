@php
    $product = $product ?? null;
    $seoTitle = old('seo_title', data_get($product?->seo, 'meta_fields.title'));
    $seoDescription = old('seo_description', data_get($product?->seo, 'meta_fields.description'));
    $seoKeywords = old('seo_keywords', data_get($product?->seo, 'meta_fields.keywords'));
@endphp

<div class="card {{ $cardClass ?? '' }}">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fa fa-search me-2"></i>SEO</h5>
        <small class="text-muted">These fields appear in search results. Leave blank to use the product title and short description.</small>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="seo_title" class="form-label">SEO Title</label>
            <input type="text" class="form-control @error('seo_title') is-invalid @enderror" id="seo_title" name="seo_title" maxlength="255" value="{{ $seoTitle }}" placeholder="{{ $product?->name ?: 'Product title is used if empty' }}">
            @error('seo_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="seo_description" class="form-label">Meta Description</label>
            <textarea class="form-control @error('seo_description') is-invalid @enderror" id="seo_description" name="seo_description" rows="3" maxlength="500" placeholder="Short summary for Google and social shares">{{ $seoDescription }}</textarea>
            @error('seo_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-0">
            <label for="seo_keywords" class="form-label">Meta Keywords</label>
            <input type="text" class="form-control @error('seo_keywords') is-invalid @enderror" id="seo_keywords" name="seo_keywords" maxlength="500" value="{{ $seoKeywords }}" placeholder="ergonomic cushion, office comfort">
            @error('seo_keywords') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        @if($product)
            <a href="{{ route('admin.products.seo.edit', $product) }}" class="btn btn-outline-secondary btn-sm mt-3">Open full SEO &amp; schema</a>
        @endif
    </div>
</div>
