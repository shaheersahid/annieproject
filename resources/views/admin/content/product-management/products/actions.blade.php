<div class="d-flex gap-2">
    @can('admin.products.show')
        <x-admin.action-button type="view" :url="route('admin.products.show', $product)" />
    @endcan

    @can('admin.products.seo.edit')
        <x-admin.action-button type="seo" :url="route('admin.products.seo.edit', $product)" tooltip="Manage SEO" />
    @endcan

    @can('admin.products.edit')
        <x-admin.action-button type="edit" :url="route('admin.products.edit', $product)" />
    @endcan

    @can('admin.products.destroy')
        <x-admin.action-button type="delete" :url="route('admin.products.destroy', $product)" />
    @endcan
</div>
