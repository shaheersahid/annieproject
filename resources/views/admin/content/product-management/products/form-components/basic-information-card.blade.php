@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
@endphp

<div class="card {{ $cardClass ?? '' }}" @if(!empty($stepGroup)) data-step-group="{{ $stepGroup }}" @endif>
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fa fa-info-circle me-2"></i>Basic Information</h5>
    </div>
    <div class="card-body">
        <input type="hidden" name="product_type" value="{{ old('product_type', $product?->product_type ?? 'accessory') }}">

        <div class="row mb-3">
            <div class="col-md-12">
                <label for="name" class="form-label fw-semibold">Product Title <span class="text-danger">*</span></label>
                <input
                    type="text"
                    class="form-control"
                    id="name"
                    name="name"
                    value="{{ old('name', $product?->name) }}"
                    @if(!$isEdit) placeholder="e.g. Ergonomic Lumbar Support Cushion" @endif
                >
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="slug" class="form-label">Slug</label>
                <input
                    type="text"
                    class="form-control"
                    id="slug"
                    name="slug"
                    value="{{ old('slug', $product?->slug) }}"
                    placeholder="Auto-generated if left empty"
                >
            </div>
            <div class="col-md-6">
                <label for="sku" class="form-label">SKU</label>
                <input
                    type="text"
                    class="form-control"
                    id="sku"
                    name="sku"
                    value="{{ old('sku', $product?->sku) }}"
                    placeholder="Auto-generated if left empty"
                >
            </div>
        </div>

        <input type="hidden" name="has_variants" value="0">

    </div>
</div>
