<template id="variant-template">
    <div class="variant-row card mb-3 border-primary">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0"><i class="fa fa-cube text-primary me-2"></i>Variant <span class="variant-number badge bg-primary"></span></h6>
            <button type="button" class="btn btn-sm btn-outline-danger remove-variant">
                <i class="fa fa-trash me-1"></i> Remove
            </button>
        </div>
        <div class="card-body">
            <input type="hidden" name="variants[__INDEX__][is_active]" value="1">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Variant Option</label>
                    <select class="form-select variant-option" name="variants[__INDEX__][attribute_id]">
                        <option value="">Select option</option>
                        @foreach(($productAttributes ?? collect()) as $attribute)
                            <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Variant Value</label>
                    <select class="form-select variant-value" name="variants[__INDEX__][value]">
                        <option value="">Select value</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Price</label>
                    <div class="input-group">
                        <span class="input-group-text">PKR</span>
                        <input type="number" step="0.01" min="0" class="form-control variant-price" name="variants[__INDEX__][price]">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Stock</label>
                    <input type="number" min="0" class="form-control variant-stock" name="variants[__INDEX__][stock]" value="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Low Stock</label>
                    <input type="number" min="0" class="form-control" name="variants[__INDEX__][low_stock_threshold]" value="5">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">SKU</label>
                    <input type="text" class="form-control variant-sku" name="variants[__INDEX__][sku]" placeholder="Optional">
                </div>
            </div>
        </div>
    </div>
</template>
