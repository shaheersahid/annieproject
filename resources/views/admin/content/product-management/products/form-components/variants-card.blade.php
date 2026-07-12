@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
@endphp

<div class="card d-none {{ $cardClass ?? '' }}" @if(!empty($stepGroup)) data-step-group="{{ $stepGroup }}" @endif id="variants-section">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0"><i class="fa fa-layer-group me-2"></i>Product Variants</h5>
        </div>
        <button type="button" class="btn btn-sm btn-primary" id="add-variant">
            <i class="fa fa-plus me-1"></i> Add Variant
        </button>
    </div>
    <div class="card-body">
        <div id="variants-container">
            @if($isEdit && $product->has_variants)
                @foreach($product->variants as $index => $variant)
                    @include('admin.content.product-management.products.single_varient', ['variant' => $variant, 'index' => $index])
                @endforeach
            @endif
        </div>
    </div>
</div>
