@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
@endphp

<div class="card {{ $cardClass ?? '' }}" @if(!empty($stepGroup)) data-step-group="{{ $stepGroup }}" @endif>
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fa fa-info-circle me-2"></i>Basic Information</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-9">
                <label for="name" class="form-label fw-semibold">Product Title <span class="text-danger">*</span></label>
                <input
                    type="text"
                    class="form-control"
                    id="name"
                    name="name"
                    value="{{ old('name', $product?->name) }}"
                    @if(!$isEdit) placeholder="e.g. RayBan Premium Eyewear Frame" @endif
                >
            </div>
            <div class="col-md-3">
                <label for="product_type" class="form-label fw-semibold">Product Type <span class="text-danger">*</span></label>
                <select class="form-select" name="product_type" id="product_type">
                    @foreach($productTypes as $type)
                        <option value="{{ $type->value }}" {{ old('product_type', $product?->product_type ?? 'frame') === $type->value ? 'selected' : '' }}>
                            {{ str($type->value)->headline() }}
                        </option>
                    @endforeach
                </select>
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

        <div class="row mb-1">
            <div class="col-md-6">
                <label for="has_variants" class="form-label">Enable Variants</label>
                <div class="form-check form-switch mt-1">
                    <input type="hidden" name="has_variants" value="0">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        id="has_variants"
                        name="has_variants"
                        value="1"
                        {{ old('has_variants', $product?->has_variants ?? false) ? 'checked' : '' }}
                    >
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small mt-md-4 pt-md-2">
                    Use variants for frame sizes/colors, lens types, prescription ranges, and accessory packs.
                </div>
            </div>
        </div>

    </div>
</div>
