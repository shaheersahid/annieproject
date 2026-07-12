<div class="d-flex gap-2">
    <x-admin.action-button type="edit" :url="route('admin.attributes.edit', $tag)" />
    <x-admin.action-button type="delete" :url="route('admin.attributes.destroy', $tag)" />
</div>
