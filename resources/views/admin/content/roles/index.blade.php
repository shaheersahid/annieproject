@extends('admin.layouts.master')
@section('page-title', 'Roles & Permissions')

@php
    $canCreateRoles = auth()->user()->can('admin.users.roles.store');
@endphp

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Roles & Permissions" :items="[['label' => 'Roles & Permissions']]" />

        <x-admin.card title="Roles & Permissions">
            <x-slot name="headerActions">
                @if($canCreateRoles)
                <button type="button" class="btn btn-primary btn-sm" id="open-create-role-modal">
                    <i class="fas fa-plus-circle me-1"></i> Add Role
                </button>
                @endif
            </x-slot>

            <x-admin.table id="roles-table" :headers="[
                'Role',
                'Status',
                'Action'
            ]" />
        </x-admin.card>
    </div>
</div>

<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="create-role-form" action="{{ route('admin.users.roles.store') }}">
                @csrf
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="createRoleModalLabel">Add Role</h5>
                        <div class="small text-muted">Set whether users can use this role.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="create-role-error-box"></div>
                    <label for="create_role_name" class="form-label">Role Name</label>
                    <input type="text" class="form-control" id="create_role_name" name="name" placeholder="e.g. stock_controller">
                    <div class="invalid-feedback" id="create_role_name_error"></div>

                    <div class="mt-3">
                        <label for="create_role_status" class="form-label">Status</label>
                        <select class="form-select" id="create_role_status" name="is_active">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-role-form">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="editRoleModalLabel">Edit Role</h5>
                        <div class="small text-muted">System roles cannot be renamed.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="edit-role-error-box"></div>
                    <label for="edit_role_name" class="form-label">Role Name</label>
                    <input type="text" class="form-control" id="edit_role_name" name="name">
                    <div class="invalid-feedback" id="edit_role_name_error"></div>

                    <div class="mt-3">
                        <label for="edit_role_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_role_status" name="is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(function() {
        const createRoleModal = new bootstrap.Modal(document.getElementById('createRoleModal'));
        const editRoleModal = new bootstrap.Modal(document.getElementById('editRoleModal'));
        const createRoleForm = $('#create-role-form');
        const editRoleForm = $('#edit-role-form');
        const updateRoleUrlTemplate = "{{ route('admin.users.roles.update', ['role' => '__ROLE__']) }}";
        const deleteRoleUrlTemplate = "{{ route('admin.users.roles.destroy', ['role' => '__ROLE__']) }}";

        const rolesTable = $('#roles-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.users.roles.index') }}",
            columns: [
                { data: 'label', name: 'name' },
                { data: 'status_badge', name: 'is_active', searchable: false },
                { data: 'action', orderable: false, searchable: false }
            ]
        });

        function reloadRoles() {
            rolesTable.ajax.reload(null, false);
        }

        function clearCreateRoleForm() {
            $('#create_role_name').val('').removeClass('is-invalid');
            $('#create_role_status').val('1');
            $('#create_role_name_error').text('');
            $('#create-role-error-box').addClass('d-none').text('');
        }

        function clearCreateRoleErrors() {
            $('#create_role_name').removeClass('is-invalid');
            $('#create_role_name_error').text('');
            $('#create-role-error-box').addClass('d-none').text('');
        }

        function clearEditRoleErrors() {
            $('#edit_role_name').removeClass('is-invalid');
            $('#edit_role_name_error').text('');
            $('#edit-role-error-box').addClass('d-none').text('');
        }

        $('#open-create-role-modal').on('click', function () {
            clearCreateRoleForm();
            createRoleModal.show();
        });

        createRoleForm.on('submit', function (e) {
            e.preventDefault();
            clearCreateRoleErrors();

            $.ajax({
                url: createRoleForm.attr('action'),
                method: 'POST',
                data: createRoleForm.serialize(),
                success: function (response) {
                    createRoleModal.hide();
                    toastr.success(response.message || 'Role created successfully.');
                    reloadRoles();
                },
                error: function (xhr) {
                    const payload = xhr.responseJSON || {};
                    if (payload.errors?.name?.length) {
                        $('#create_role_name').addClass('is-invalid');
                        $('#create_role_name_error').text(payload.errors.name[0]);
                    }

                    $('#create-role-error-box').removeClass('d-none').text(payload.message || 'Unable to create this role.');
                }
            });
        });

        $(document).on('click', '.btn-edit-role', function () {
            clearEditRoleErrors();
            editRoleForm.attr('action', updateRoleUrlTemplate.replace('__ROLE__', $(this).data('role-id')));
            $('#edit_role_name').val($(this).data('role-name')).prop('readonly', String($(this).data('protected')) === '1');
            $('#edit_role_status').val(String($(this).data('role-active')));
            $('#editRoleModalLabel').text('Edit Role: ' + $(this).data('role-label'));
            editRoleModal.show();
        });

        editRoleForm.on('submit', function (e) {
            e.preventDefault();
            clearEditRoleErrors();

            $.ajax({
                url: editRoleForm.attr('action'),
                method: 'POST',
                data: editRoleForm.serialize(),
                success: function (response) {
                    editRoleModal.hide();
                    toastr.success(response.message || 'Role updated successfully.');
                    reloadRoles();
                },
                error: function (xhr) {
                    const payload = xhr.responseJSON || {};
                    if (payload.errors?.name?.length) {
                        $('#edit_role_name').addClass('is-invalid');
                        $('#edit_role_name_error').text(payload.errors.name[0]);
                    }

                    $('#edit-role-error-box').removeClass('d-none').text(payload.message || 'Unable to update this role.');
                }
            });
        });

        $(document).on('click', '.btn-delete-role', function () {
            const roleId = $(this).data('role-id');
            const roleName = $(this).data('role-name');

            Swal.fire({
                title: 'Delete role?',
                text: 'Delete "' + roleName + '" only if no users still depend on it.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it'
            }).then(function (result) {
                if (! result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: deleteRoleUrlTemplate.replace('__ROLE__', roleId),
                    method: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: createRoleForm.find('input[name="_token"]').val()
                    },
                    success: function (response) {
                        toastr.success(response.message || 'Role deleted successfully.');
                        reloadRoles();
                    },
                    error: function (xhr) {
                        Swal.fire('Unable to delete role', xhr.responseJSON?.message || 'This role could not be deleted.', 'error');
                    }
                });
            });
        });
    });
</script>
@endpush
