@php
    $isEdit           = $isEdit ?? false;
    $product          = $product ?? null;
    $productDiscounts = $productDiscounts ?? collect();
    $productBundles   = $productBundles   ?? collect();
@endphp

{{-- Merchandising card — only rendered on edit form --}}
@if($isEdit && $product)
<div class="card {{ $cardClass ?? '' }}" @if(!empty($stepGroup)) data-step-group="{{ $stepGroup }}" @endif>
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="fa fa-bullhorn me-2"></i>Merchandising & Offers</h5>
    </div>
    <div class="card-body">
        {{-- Toggles --}}
        <!-- <div class="form-check form-switch mb-3">
            <input type="hidden" name="is_featured" value="0">
            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_featured">Featured Product</label>
        </div>
        <div class="form-check form-switch mb-4">
            <input type="hidden" name="is_popular" value="0">
            <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular" value="1" {{ old('is_popular', $product->is_popular) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_popular">Popular Product</label>
        </div> -->

        {{-- Counts / Badges --}}
        <div class="d-flex gap-2 mb-4">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" id="discount-count-badge">
                <i class="fa fa-tag me-1"></i> <span class="count-value">{{ $productDiscounts->count() }}</span> product discounts
            </span>
            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" id="bundle-count-badge">
                <i class="fa fa-cubes me-1"></i> <span class="count-value">{{ $productBundles->count() }}</span> bundles
            </span>
        </div>

        {{-- Action Buttons --}}
        <div class="d-grid gap-2 mb-4">
            <button type="button" class="btn btn-outline-danger btn-sm py-2" data-bs-toggle="modal" data-bs-target="#quickDiscountModal">
                <i class="fa fa-plus me-1"></i> Create Product Discount
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm py-2" data-bs-toggle="modal" data-bs-target="#quickBundleModal">
                <i class="fa fa-plus me-1"></i> Create Bundle
            </button>
        </div>

        {{-- Linked Discounts List --}}
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0" style="font-size: 0.85rem;">Linked Discounts</h6>
                <a href="{{ route('admin.discounts.index') }}" class="text-danger small fw-semibold">View all</a>
            </div>
            <div class="list-group list-group-flush border-top" id="linked-discounts-list">
                @forelse($productDiscounts as $discount)
                <a href="{{ route('admin.discounts.edit', $discount) }}" class="list-group-item list-group-item-action px-0 py-2 border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-dark small fw-semibold text-truncate me-2">{{ $discount->name }}</div>
                        <span class="badge {{ $discount->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2 py-1" style="font-size: 0.7rem;">
                            {{ $discount->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </a>
                @empty
                <p id="linked-discounts-empty" class="text-muted small mb-0 mt-2">No product-specific discounts yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Bundles List --}}
        <div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0" style="font-size: 0.85rem;">Bundles Using This Product</h6>
                <a href="{{ route('admin.bundles.index') }}" class="text-danger small fw-semibold">View all</a>
            </div>
            <div class="list-group list-group-flush border-top" id="linked-bundles-list">
                @forelse($productBundles as $bundle)
                <a href="{{ route('admin.bundles.edit', $bundle) }}" class="list-group-item list-group-item-action px-0 py-2 border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-dark small fw-semibold text-truncate me-2">{{ $bundle->name }}</div>
                        <span class="badge {{ $bundle->is_active ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' }} px-2 py-1" style="font-size: 0.7rem;">
                            {{ $bundle->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </a>
                @empty
                <p id="linked-bundles-empty" class="text-muted small mb-0 mt-2">No bundles include this product yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif

