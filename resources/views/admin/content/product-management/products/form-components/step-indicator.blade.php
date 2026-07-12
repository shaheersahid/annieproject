@php
    $steps = $steps ?? [
        ['number' => 1, 'label' => 'Basics', 'meta' => 'Name, category, suppliers'],
        ['number' => 2, 'label' => 'Media', 'meta' => 'Thumbnail, gallery, video'],
        ['number' => 3, 'label' => 'Pricing & Stock', 'meta' => 'Price, VAT, inventory'],
        ['number' => 4, 'label' => 'Details', 'meta' => 'Specs, publish, merchandising'],
    ];
    $activeStep = $activeStep ?? 1;
@endphp

<div class="step-shell">
    <div class="step-indicator">
        @foreach ($steps as $step)
            <div class="step-item {{ $step['number'] === $activeStep ? 'active' : '' }}" data-step="{{ $step['number'] }}" role="button" tabindex="0">
                <div class="step-number">{{ $step['number'] }}</div>
                <div>
                    <span class="step-label">{{ $step['label'] }}</span>
                    <span class="step-meta">{{ $step['meta'] }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
