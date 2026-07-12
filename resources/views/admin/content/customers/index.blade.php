@extends('admin.layouts.master')
@section('page-title', 'Customers')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Customers" :items="[['label' => 'Customers']]" />

        <x-admin.card title="Customers">
            <x-admin.table id="customers-table" :headers="[
                'Name',
                'Email',
                'Phone',
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
        $('#customers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.customers.index') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone', defaultContent: '-' },
                { data: 'total_orders', name: 'total_orders', searchable: false, orderable: false },
                { data: 'total_spent', name: 'total_spent', searchable: false, orderable: false },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
