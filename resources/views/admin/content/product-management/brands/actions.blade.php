<div class="d-flex gap-2">
    <x-admin.action-button type="edit" :url="route('admin.brands.edit', $brand)" />
    <x-admin.action-button type="delete" :url="route('admin.brands.destroy', $brand)" />
</div>
