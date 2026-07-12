@extends('admin.layouts.master')
@section('page-title', 'Users')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Users" :items="[['label' => 'Users']]" />

        <x-admin.card title="Users">
            <x-slot name="headerActions">
                @can('admin.users.store')
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Add New User
                </a>
                @endcan
            </x-slot>

            <x-admin.table id="users-table" :headers="[
                'Name',
                'Email',
                'Phone',
                'Role',
                'Total Orders',
                'Total Spent',
                'Action'
            ]" />
        </x-admin.card>
    </div>
</div>
@endsection

@push('admin-scripts')
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(function() {
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.users.index') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone', defaultContent: '-' },
                { data: 'role', name: 'roles.name', searchable: false },
                { data: 'total_orders', name: 'total_orders', searchable: false, orderable: false },
                { data: 'total_spent', name: 'total_spent', searchable: false, orderable: false },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
