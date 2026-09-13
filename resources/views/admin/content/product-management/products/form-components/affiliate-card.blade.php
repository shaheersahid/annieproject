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
            <div class="col-md-8">
                <label for="tiktok_url" class="form-label">TikTok / Reel URL</label>
                <input type="url" class="form-control" id="tiktok_url" name="tiktok_url" value="{{ old('tiktok_url', $product?->tiktok_url) }}" placeholder="https://www.tiktok.com/@user/video/...">
            </div>
            <input type="hidden" id="price_note" name="price_note" value="{{ old('price_note', $product?->price_note ?? 'Check latest price') }}">
            @php
                $featuredHomeCategories = $featuredHomeCategories ?? collect();
                $selectedFeaturedTabs = collect(old('featured_category_ids', $product?->featured_category_ids ?? []))
                    ->map(fn ($id) => (int) $id)
                    ->all();
            @endphp
            <div class="col-12">
                <label class="form-label">Featured Comfort Deals tabs</label>
                <p class="text-muted small mb-2">Choose which homepage tabs should show this product. Leave both unchecked to keep it out of Featured Comfort Deals.</p>
                <input type="hidden" name="featured_tabs_managed" value="1">
                <div class="d-flex flex-wrap gap-3">
                    @forelse($featuredHomeCategories as $featuredCategory)
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="featured_tab_{{ $featuredCategory->id }}"
                                name="featured_category_ids[]"
                                value="{{ $featuredCategory->id }}"
                                @checked(in_array((int) $featuredCategory->id, $selectedFeaturedTabs, true))
                            >
                            <label class="form-check-label" for="featured_tab_{{ $featuredCategory->id }}">
                                {{ $featuredCategory->name }}
                            </label>
                        </div>
                    @empty
                        <span class="text-muted small">No homepage featured tabs found.</span>
                    @endforelse
                </div>
                @if($isEdit && $product)
                    <div class="mt-2">
                        <a href="{{ route('admin.featured-deals.index') }}" class="small">Open Featured Deals manager</a>
                    </div>
                @endif
            </div>
            <div class="col-md-3">
                <label for="is_latest" class="form-label">Latest Deals Page</label>
                <div class="form-check form-switch mt-1">
                    <input type="hidden" name="is_latest" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_latest" name="is_latest" value="1" {{ old('is_latest', $product?->is_latest ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_latest">Show as latest pick</label>
                </div>
            </div>
            <div class="col-md-3">
                <label for="is_reel" class="form-label">TikTok / Reel Pick</label>
                <div class="form-check form-switch mt-1">
                    <input type="hidden" name="is_reel" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_reel" name="is_reel" value="1" {{ old('is_reel', $product?->is_reel ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_reel">Pin to top of Latest Deals</label>
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
