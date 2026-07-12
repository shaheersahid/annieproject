<div class="d-flex justify-content-center gap-2">
    <!-- @can('edit categories')
        <x-admin.action-button type="sort" :url="route('admin.categories.products', $category->id)" />
    @endcan -->

    @can('edit categories')
        <x-admin.action-button type="edit" :url="route('admin.categories.edit', $category->id)" />
    @endcan
    
    @can('delete categories')
        <x-admin.action-button type="delete" :url="route('admin.categories.destroy', $category->id)" />
    @endcan
</div>
