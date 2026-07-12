<div class="d-flex gap-1">
    <x-admin.action-button :url="route('admin.customers.points.show', $user)" type="view" />

    <a href="{{ route('admin.customers.points.create', $user) }}" class="btn btn-sm btn-primary" title="Adjust Points">
        <i class="fa fa-adjust"></i>
    </a>
</div>
