@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
@endphp

<div class="card {{ $cardClass ?? '' }}" @if(!empty($stepGroup)) data-step-group="{{ $stepGroup }}" @endif>
    <div class="card-header">
        <h5 class="card-title mb-0">Publish Options</h5>
    </div>
    <div class="card-body">
        <!-- Active Option -->
        <div class="form-check form-switch mb-3">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" id="is_active_switch" name="is_active" value="1" {{ old('is_active', $isEdit && $product ? $product->is_active : true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active_switch">Active</label>
        </div>
        
        <!-- Out of Stock Option -->
        <div class="form-check form-switch mb-3">
            <input type="hidden" name="out_of_stock" value="0">
            <input class="form-check-input" type="checkbox" id="out_of_stock_switch" name="out_of_stock" value="1" {{ old('out_of_stock', $isEdit && $product ? $product->out_of_stock : false) ? 'checked' : '' }}>
            <label class="form-check-label" for="out_of_stock_switch">Out of Stock</label>
        </div>
    </div>
</div>
