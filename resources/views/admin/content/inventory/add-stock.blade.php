@extends('admin.layouts.master')
@section('page-title', 'Add Stock')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Add Stock" :items="[['label' => 'Stock Products', 'url' => route('admin.products.stock-products')], ['label' => 'Add Stock']]" />

        <div class="row">
            <div class="col-lg-7">
                <x-admin.card title="Stock Adjustment">
                    <form method="POST" action="{{ route('admin.products.stocks.add-stock.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="product_id">Product</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                <option value="">Select product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                        {{ $product->name }} ({{ $product->sku ?? 'No SKU' }}) - Stock: {{ $product->stock }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="variant_id">Variant</label>
                            <select class="form-select @error('variant_id') is-invalid @enderror" id="variant_id" name="variant_id">
                                <option value="">Whole Product</option>
                            </select>
                            <div class="form-text" id="variant-help">Select a product to load variants.</div>
                            @error('variant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="type">Adjustment Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="in" @selected(old('type', 'in') === 'in')>Stock In</option>
                                <option value="out" @selected(old('type') === 'out')>Stock Out</option>
                                <option value="set" @selected(old('type') === 'set')>Set Exact Stock</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="quantity">Quantity</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" min="0" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="note">Note</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Adjustment
                            </button>
                            <a href="{{ route('admin.products.stock-products') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </x-admin.card>
            </div>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
    $(function() {
        const variantsByProduct = @json($variantsByProduct);

        const oldVariantId = "{{ old('variant_id') }}";

        function refreshVariants() {
            const productId = $('#product_id').val();
            const variants = variantsByProduct[productId] || [];
            const $variant = $('#variant_id');

            $variant.empty().append(new Option('Whole Product', ''));

            variants.forEach(function(variant) {
                const option = new Option(variant.label + ' - Stock: ' + variant.stock, variant.id);
                if (String(variant.id) === oldVariantId) {
                    option.selected = true;
                }
                $variant.append(option);
            });

            $('#variant-help').text(variants.length ? 'Optional: choose a variant to adjust variant stock.' : 'This product has no variants.');
        }

        $('#product_id').on('change', refreshVariants);
        refreshVariants();
    });
</script>
@endpush
