@can('adjust inventory')
<button class="btn btn-sm btn-info adjust-stock-btn"
    data-id="{{ $product->id }}"
    data-has-variants="{{ $product->has_variants ? 1 : 0 }}">
    <i class="fas fa-sliders-h"></i> Adjust
</button>
@endcan
