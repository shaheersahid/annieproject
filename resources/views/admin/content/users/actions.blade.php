<div class="d-flex gap-2">
    @if(auth()->user()->canManage($user))
        <x-admin.action-button type="view" :url="route('admin.users.show', $user)" />
    @endif

    @if(auth()->user()->can('admin.users.update') && auth()->user()->canManage($user))
       <x-admin.action-button type="edit" :url="route('admin.users.edit', $user)" />
    @endif


    @if(auth()->user()->can('admin.users.destroy') && auth()->user()->canDelete($user) && auth()->id() !== $user->id)
        <x-admin.action-button type="delete" :url="route('admin.users.destroy', $user)" />
    @endif
</div>
