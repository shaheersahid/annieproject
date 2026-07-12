<div class="d-flex gap-2">
    <a href="{{ route('admin.products.stocks.order-requests.show', $product) }}" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Add Stock">
        <i class="fas fa-plus-circle"></i>
    </a>
    <x-admin.action-button type="edit" :url="route('admin.products.edit', $product)" />
</div>
