@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
    $specifications = $isEdit && $product ? ($product->specifications ?? []) : [];
@endphp

<div class="card {{ $cardClass ?? '' }}" @if(!empty($stepGroup)) data-step-group="{{ $stepGroup }}" @endif id="weight-details-card">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fa fa-ruler-combined me-2"></i>Specifications & Dimensions</h5>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">Lens Width</label>
                    <input type="number" step="0.01" class="form-control" name="specifications[length]" 
                           value="{{ old('specifications.length', $specifications['length'] ?? '') }}" placeholder="0.00">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">Bridge Width</label>
                    <input type="number" step="0.01" class="form-control" name="specifications[width]" 
                           value="{{ old('specifications.width', $specifications['width'] ?? '') }}" placeholder="0.00">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">Temple Length</label>
                    <input type="number" step="0.01" class="form-control" name="specifications[height]" 
                           value="{{ old('specifications.height', $specifications['height'] ?? '') }}" placeholder="0.00">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">Unit</label>
                    <select class="form-select" name="specifications[dimension_unit]">
                        <option value="cm" {{ old('specifications.dimension_unit', $specifications['dimension_unit'] ?? 'cm') === 'cm' ? 'selected' : '' }}>cm</option>
                        <option value="mm" {{ old('specifications.dimension_unit', $specifications['dimension_unit'] ?? 'cm') === 'mm' ? 'selected' : '' }}>mm</option>
                        <option value="in" {{ old('specifications.dimension_unit', $specifications['dimension_unit'] ?? 'cm') === 'in' ? 'selected' : '' }}>in</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-0">
            <label class="form-label fw-bold" id="details-specs-heading">Optical Specifications</label>
            <p class="text-muted small mb-3" id="details-specs-help">Use this section for material, shape, lens coating, prescription notes, warranty, or care instructions.</p>
            
            <div id="specs-container">
                @php
                    $items = $specifications['items'] ?? [];
                    // Fallback for old structure if exists
                    if (empty($items) && !empty($specifications) && isset($specifications[0]['key'])) {
                        $items = $specifications;
                    }
                @endphp
                @foreach($items as $index => $spec)
                    @if(isset($spec['key']) && isset($spec['value']))
                    <div class="spec-row row mb-2">
                        <div class="col-5">
                            <input type="text" class="form-control" name="specifications[items][{{ $index }}][key]" value="{{ $spec['key'] }}" placeholder="Specification name">
                        </div>
                        <div class="col-5">
                            <input type="text" class="form-control" name="specifications[items][{{ $index }}][value]" value="{{ $spec['value'] }}" placeholder="Value">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-outline-danger remove-spec">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            
            <button type="button" class="btn btn-outline-danger btn-sm mt-2" id="add-spec">
                <i class="fa fa-plus me-1"></i> Add Specification
            </button>
        </div>
    </div>
</div>
