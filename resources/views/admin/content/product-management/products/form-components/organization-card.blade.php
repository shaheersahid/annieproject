@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
    $selectedCategories = collect(old('category_ids', $product?->categories?->pluck('id') ?? []));
    $selectedTags = collect(old('tag_ids', $product?->tags?->pluck('id') ?? []));
@endphp

<div class="card {{ $cardClass ?? '' }}" @if(!empty($stepGroup)) data-step-group="{{ $stepGroup }}" @endif>
    <div class="card-header">
        <h5 class="card-title mb-0">Organization</h5>
    </div>
    <div class="card-body">
        <div class="mb-0">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <label for="category_ids" class="form-label mb-0">Category <span class="text-danger">*</span></label>
            </div>
            <select class="form-select select2-categories" id="category_ids" name="category_ids[]" multiple="multiple">
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ ($selectedCategories->contains($cat->id)) ? 'selected' : '' }}>
                        {{ $cat->parent ? $cat->parent->name . ' > ' : '' }}{{ $cat->name }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">{{ $isEdit ? 'Select one or more categories.' : 'Select one or more categories.' }}</small>
        </div>

        <div class="mt-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <label for="brand_id" class="form-label mb-0">Brand</label>
            </div>
            <select class="form-select" id="brand_id" name="brand_id">
                <option value="">No Brand</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $product?->brand_id) == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mt-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <label for="seller_id" class="form-label mb-0">Seller</label>
            </div>
            <select class="form-select" id="seller_id" name="seller_id">
                <option value="">No Seller</option>
                @foreach (($sellers ?? collect()) as $seller)
                    <option value="{{ $seller->id }}" {{ old('seller_id', $product?->seller_id) == $seller->id ? 'selected' : '' }}>
                        {{ $seller->store_name }} ({{ $seller->owner_name }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mt-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <label for="tag_ids" class="form-label mb-0">Tags</label>
            </div>
            <select class="form-select select2" id="tag_ids" name="tag_ids[]" multiple="multiple">
                @foreach (($productTags ?? collect()) as $tag)
                    <option value="{{ $tag->id }}" {{ $selectedTags->contains($tag->id) ? 'selected' : '' }}>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
        </div>

        @if($isEdit)
            <div class="mt-3" id="size-chart-wrapper" style="{{ in_array(old('product_type', $product?->product_type ?? 'frame'), ['frame', 'service'], true) ? '' : 'display: none;' }}">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label for="size_chart_id" class="form-label mb-0">Fit / Prescription Guide</label>
                </div>
                <select class="form-select" id="size_chart_id" name="size_chart_id">
                    <option value="">No Guide</option>
                    @foreach ($sizeCharts as $sizeChart)
                        <option value="{{ $sizeChart->id }}" {{ old('size_chart_id', $product?->size_chart_id) == $sizeChart->id ? 'selected' : '' }}>
                            {{ $sizeChart->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
</div>
