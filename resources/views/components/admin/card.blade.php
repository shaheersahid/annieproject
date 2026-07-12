@props([
    'title' => '',
    'headerActions' => null,
    'footer' => null,
    'class' => '',
    'bodyClass' => ''
])

<div class="card {{ $class }} border-0 shadow-sm">
    @if($title || $headerActions)
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                @if($title)
                    <h5 class="card-title mb-0 fw-bold">{{ $title }}</h5>
                @endif
                @if($headerActions)
                    <div class="d-flex align-items-center gap-2">
                        {{ $headerActions }}
                    </div>
                @endif
            </div>
        </div>
    @endif
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
    @if($footer)
        <div class="card-footer bg-white border-top py-3">
            {{ $footer }}
        </div>
    @endif
</div>
