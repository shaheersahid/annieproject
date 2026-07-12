@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
@endphp

<div class="card {{ $cardClass ?? '' }}" @if(!empty($stepGroup)) data-step-group="{{ $stepGroup }}" @endif>
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="fa fa-file-lines me-2"></i>Description</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="short_description" class="form-label mb-0">Short Description</label>
                </div>
                <textarea class="form-control" id="short_description" name="short_description" rows="2">{{ old('short_description', $product?->short_description) }}</textarea>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="description" class="form-label mb-0">Description <span class="text-danger">*</span></label>
                </div>
                <textarea class="form-control" id="description" name="description" rows="5">{{ old('description', $product?->description) }}</textarea>
            </div>
        </div>
    </div>
</div>
