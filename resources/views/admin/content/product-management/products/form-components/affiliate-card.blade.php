@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
@endphp

<div class="card {{ $cardClass ?? '' }}">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fa fa-link me-2"></i>Affiliate Marketing</h5>
        <small class="text-muted">Use this product as an Amazon/Temu outbound deal instead of an internal checkout item.</small>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="affiliate_platform" class="form-label fw-semibold">Platform</label>
                <select class="form-select" name="affiliate_platform" id="affiliate_platform">
                    @foreach(['none' => 'None', 'amazon' => 'Amazon', 'temu' => 'Temu', 'aliexpress' => 'AliExpress', 'both' => 'Amazon + Temu', 'all' => 'All Platforms'] as $value => $label)
                        <option value="{{ $value }}" {{ old('affiliate_platform', $product?->affiliate_platform ?? 'none') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="external_product_id" class="form-label">External Product ID</label>
                <input type="text" class="form-control" id="external_product_id" name="external_product_id" value="{{ old('external_product_id', $product?->external_product_id) }}" placeholder="ASIN / Temu SKU">
            </div>
            <div class="col-md-4">
                <label for="affiliate_rating" class="form-label">Editorial Rating</label>
                <input type="number" step="0.1" min="0" max="5" class="form-control" id="affiliate_rating" name="affiliate_rating" value="{{ old('affiliate_rating', $product?->affiliate_rating) }}" placeholder="4.5">
            </div>
            <div class="col-md-4">
                <label for="amazon_url" class="form-label">Amazon Affiliate URL</label>
                <input type="url" class="form-control" id="amazon_url" name="amazon_url" value="{{ old('amazon_url', $product?->amazon_url) }}" placeholder="https://www.amazon.com/...tag=yourtag-20">
            </div>
            <div class="col-md-4">
                <label for="temu_url" class="form-label">Temu Affiliate URL</label>
                <input type="url" class="form-control" id="temu_url" name="temu_url" value="{{ old('temu_url', $product?->temu_url) }}" placeholder="https://temu.com/...">
            </div>
            <div class="col-md-4">
                <label for="aliexpress_url" class="form-label">AliExpress Affiliate URL</label>
                <input type="url" class="form-control" id="aliexpress_url" name="aliexpress_url" value="{{ old('aliexpress_url', $product?->aliexpress_url) }}" placeholder="https://www.aliexpress.com/...aff_id=...">
            </div>
            <input type="hidden" id="price_note" name="price_note" value="{{ old('price_note', $product?->price_note ?? 'Check latest price') }}">
            <div class="col-md-6">
                <label for="is_featured" class="form-label">Featured Deal</label>
                <div class="form-check form-switch mt-1">
                    <input type="hidden" name="is_featured" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product?->is_featured ?? false) ? 'checked' : '' }}>
                </div>
            </div>
            <div class="col-md-6">
                <label for="pros" class="form-label">Pros</label>
                <textarea class="form-control" id="pros" name="pros" rows="4" placeholder="One benefit per line">{{ old('pros', implode("\n", $product?->pros ?? [])) }}</textarea>
            </div>
            <div class="col-md-6">
                <label for="cons" class="form-label">Cons</label>
                <textarea class="form-control" id="cons" name="cons" rows="4" placeholder="One limitation per line">{{ old('cons', implode("\n", $product?->cons ?? [])) }}</textarea>
            </div>
        </div>
    </div>
</div>
