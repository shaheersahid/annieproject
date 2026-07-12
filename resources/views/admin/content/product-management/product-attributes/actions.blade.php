<div class="d-flex gap-2">
    <x-admin.action-button type="edit" :url="route('admin.product-attributes.edit', $attribute)" />
    <x-admin.action-button type="delete" :url="route('admin.product-attributes.destroy', $attribute)" />
</div>
