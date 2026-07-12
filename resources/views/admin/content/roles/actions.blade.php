<div class="d-flex gap-2">
    @can('admin.users.roles.update')
    <button
        type="button"
        class="btn btn-sm btn-outline-secondary btn-edit-role"
        data-role-id="{{ $role->id }}"
        data-role-name="{{ $role->name }}"
        data-role-label="{{ Str::headline($role->name) }}"
        data-role-active="{{ $role->is_active ? '1' : '0' }}"
        data-protected="{{ $protected ? '1' : '0' }}">
        <i class="fas fa-edit"></i>
    </button>
    @endcan

    @can('admin.users.roles.permissions.edit')
    <a href="{{ route('admin.users.roles.permissions.edit', $role) }}" class="btn btn-sm btn-outline-primary">
        <i class="fas fa-shield-alt"></i>
    </a>
    @endcan

    @if(! $protected)
        @can('admin.users.roles.destroy')
        <button
            type="button"
            class="btn btn-sm btn-outline-danger btn-delete-role"
            data-role-id="{{ $role->id }}"
            data-role-name="{{ Str::headline($role->name) }}">
            <i class="fas fa-trash"></i>
        </button>
        @endcan
    @endif
</div>
