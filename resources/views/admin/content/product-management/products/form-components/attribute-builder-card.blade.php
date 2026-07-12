@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
@endphp

<div class="card {{ $cardClass ?? '' }}" id="attribute-builder-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="fa fa-sliders-h me-2"></i>Attribute Builder</h5>
        <span class="badge bg-warning-subtle text-warning">Phase 2.1 Preview</span>
    </div>
    <div class="card-body">
        <p class="text-muted mb-2">
            Attribute and variation generation UI is currently feature-flag gated and non-destructive.
        </p>
        @if ($isEdit && $product)
            <small class="text-muted">Product ID: {{ $product->id }}</small>
        @endif
    </div>
</div>
