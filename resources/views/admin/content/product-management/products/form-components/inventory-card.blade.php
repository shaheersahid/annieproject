@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
@endphp

<div class="card {{ $cardClass ?? '' }}" @if(!empty($stepGroup)) data-step-group="{{ $stepGroup }}" @endif id="inventory-section">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fa fa-warehouse me-2"></i>Stock</h5>
        <small class="text-muted">Track the actual product stock fields</small>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <label for="stock_quantity" class="form-label fw-semibold">Available Stock <span class="text-danger">*</span></label>
                <input type="number" name="stock" id="stock_quantity" class="form-control" 
                    value="{{ old('stock', $isEdit ? $product->stock : 0) }}" min="0" required>
                <small class="text-info d-none" id="stock_quantity_help"></small>
            </div>
            <div class="col-md-12">
                <label for="sold_out" class="form-label fw-semibold">Sold Out</label>
                <input type="number" name="sold_out" id="sold_out" class="form-control"
                    value="{{ old('sold_out', $isEdit ? $product->sold_out : 0) }}" min="0">
                <small class="text-muted">Total units sold or marked as sold out.</small>
            </div>
        </div>
    </div>
</div>
