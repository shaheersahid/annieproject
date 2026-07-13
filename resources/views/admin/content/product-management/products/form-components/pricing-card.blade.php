@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
@endphp

<div class="card simple-product-only {{ $cardClass ?? '' }}" @if(!empty($stepGroup)) data-step-group="{{ $stepGroup }}" @endif id="pricing-section">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fa fa-pound-sign me-2"></i>Pricing</h5>
        <small class="text-muted simple-product-only">Manage base price, sale price, and product deal settings</small>
    </div>
    <div class="card-body" id="simple-pricing">
        <div class="row g-3">
            <div class="col-md-6 affiliate-price-field">
                <label for="base_price" class="form-label fw-semibold">Base Price <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">PKR</span>
                    <input type="number" step="0.01" min="0" class="form-control"
                        id="base_price" name="base_price" value="{{ old('base_price', $isEdit ? $product->base_price : '') }}"
                        placeholder="e.g. 29.99">
                </div>
                <small class="text-muted">The regular price of the product</small>
            </div>
            <div class="col-md-3 affiliate-price-field">
                <label for="sale_price" class="form-label fw-semibold">Sale Price</label>
                <div class="input-group">
                    <span class="input-group-text">PKR</span>
                    <input type="number" step="0.01" min="0" class="form-control"
                        id="sale_price" name="sale_price" value="{{ old('sale_price', $isEdit ? $product->sale_price : '') }}"
                        placeholder="e.g. 19.99">
                </div>
                <small class="text-muted">Optional discounted sale price</small>
            </div>
            <div class="col-md-3">
                <label for="stock_quantity" class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                <input type="number" name="stock" id="stock_quantity" class="form-control"
                    value="{{ old('stock', $isEdit ? $product->stock : 0) }}" min="0" required>
                <small class="text-info d-none" id="stock_quantity_help"></small>
            </div>
            <div class="col-md-3">
                <label for="low_stock_threshold" class="form-label fw-semibold">Low Stock Alert</label>
                <input type="number" name="low_stock_threshold" id="low_stock_threshold" class="form-control"
                    value="{{ old('low_stock_threshold', $isEdit ? $product->low_stock_threshold : 5) }}" min="0">
                <small class="text-muted">Dashboard alert threshold</small>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex align-items-center justify-content-between p-3 bg-light border rounded affiliate-price-field">
            <div>
                <label class="form-label fw-semibold mb-1" for="is_deal">Enable Deal</label>
                <div class="text-muted small">Use this for fixed or percentage promotional pricing.</div>
            </div>
            <div class="form-check form-switch mb-0">
                <input type="hidden" name="is_deal" value="0">
                <input type="hidden" name="deal_enabled" id="deal_enabled" value="{{ old('deal_enabled', old('is_deal', $isEdit ? $product->deal_enabled : false)) ? 1 : 0 }}">
                <input class="form-check-input" type="checkbox" name="is_deal" id="is_deal" value="1" {{ old('is_deal', $isEdit ? $product->deal_enabled : false) ? 'checked' : '' }}>
            </div>
        </div>

        <div id="deal-settings-wrapper" class="mt-3 affiliate-price-field" style="display: {{ old('is_deal', $isEdit ? $product->deal_enabled : false) ? 'block' : 'none' }};">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="deal_type" class="form-label fw-semibold">Deal Type</label>
                    <select class="form-select" name="deal_type" id="deal_type">
                        <option value="">Select type</option>
                        <option value="fixed" {{ old('deal_type', $isEdit ? $product->deal_type : '') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage" {{ old('deal_type', $isEdit ? $product->deal_type : '') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="deal_value" class="form-label fw-semibold">Deal Value</label>
                    <input type="number" step="0.01" min="0" class="form-control"
                        id="deal_value" name="deal_value" value="{{ old('deal_value', $isEdit ? $product->deal_value : '') }}"
                        placeholder="e.g. 10">
                </div>
                <div class="col-md-4">
                    <label for="deal_start_at" class="form-label fw-semibold">Deal Start</label>
                    <input type="datetime-local" class="form-control" id="deal_start_at" name="deal_start_at"
                        value="{{ old('deal_start_at', $isEdit && $product->deal_start_at ? $product->deal_start_at->format('Y-m-d\\TH:i') : '') }}">
                </div>
                <div class="col-md-4">
                    <label for="deal_end_at" class="form-label fw-semibold">Deal End</label>
                    <input type="datetime-local" class="form-control" id="deal_end_at" name="deal_end_at"
                        value="{{ old('deal_end_at', $isEdit && $product->deal_end_at ? $product->deal_end_at->format('Y-m-d\\TH:i') : '') }}">
                </div>
            </div>
        </div>
    </div>
</div>
