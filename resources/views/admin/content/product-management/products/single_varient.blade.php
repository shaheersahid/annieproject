@php
    $index = $index ?? 0;
    $variant = $variant ?? null;
    $attrs = $variant ? (is_array($variant->attributes) ? $variant->attributes : []) : [];
    $selectedAttributeName = array_key_first($attrs ?: []);
    $selectedAttribute = ($productAttributes ?? collect())->firstWhere('name', $selectedAttributeName);
    $selectedValue = $attrs[$selectedAttributeName] ?? '';
@endphp

<div class="variant-row card mb-3 border-primary">
    @if($variant)
        <input type="hidden" name="variants[{{ $index }}][id]" class="variant-id" value="{{ $variant->id }}">
    @endif
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0"><i class="fa fa-cube text-primary me-2"></i>Variant <span class="variant-number badge bg-primary">{{ $index + 1 }}</span></h6>
        <button type="button" class="btn btn-sm btn-outline-danger remove-variant" @disabled($index === 0)>
            <i class="fa fa-trash me-1"></i> Remove
        </button>
    </div>
    <div class="card-body">
        <input type="hidden" name="variants[{{ $index }}][is_active]" value="1">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Variant Option</label>
                <select class="form-select variant-option" name="variants[{{ $index }}][attribute_id]">
                    <option value="">Select option</option>
                    @foreach(($productAttributes ?? collect()) as $attribute)
                        <option value="{{ $attribute->id }}" @selected($selectedAttribute?->id === $attribute->id)>{{ $attribute->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Variant Value</label>
                <select class="form-select variant-value" name="variants[{{ $index }}][value]" data-selected="{{ $selectedValue }}">
                    <option value="">Select value</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Price</label>
                <div class="input-group">
                    <span class="input-group-text">PKR</span>
                    <input type="number" step="0.01" min="0" class="form-control variant-price" name="variants[{{ $index }}][price]" value="{{ $variant?->price }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Stock</label>
                <input type="number" min="0" class="form-control variant-stock" name="variants[{{ $index }}][stock]" value="{{ $variant?->stock ?? 0 }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Low Stock</label>
                <input type="number" min="0" class="form-control" name="variants[{{ $index }}][low_stock_threshold]" value="{{ $variant?->low_stock_threshold ?? 5 }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">SKU</label>
                <input type="text" class="form-control variant-sku" name="variants[{{ $index }}][sku]" value="{{ $variant?->sku }}" placeholder="Optional">
            </div>
        </div>
    </div>
</div>
