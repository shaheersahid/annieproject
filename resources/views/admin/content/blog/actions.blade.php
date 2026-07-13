<div class="d-flex justify-content-center gap-2">
    <x-admin.action-button type="edit" :url="route('admin.blog.edit', $blog->id)" />
    <x-admin.action-button type="delete" :url="route('admin.blog.destroy', $blog->id)" />
</div>
