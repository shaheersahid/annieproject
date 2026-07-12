<div class="d-flex gap-2">
    <x-admin.action-button type="edit" :url="route('admin.sellers.edit', $seller)" />
    <x-admin.action-button type="delete" :url="route('admin.sellers.destroy', $seller)" />
</div>
