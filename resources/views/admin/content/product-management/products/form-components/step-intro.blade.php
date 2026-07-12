<div class="step-section-intro" @if(!empty($id)) id="{{ $id }}" @endif>
    <div class="fw-semibold text-dark mb-1" @if(!empty($titleId)) id="{{ $titleId }}" @endif>{{ $title }}</div>
    <p class="text-muted small mb-0" @if(!empty($textId)) id="{{ $textId }}" @endif>{{ $text }}</p>
</div>
