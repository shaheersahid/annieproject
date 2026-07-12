@extends('admin.layouts.master')
@section('page-title', 'All Orders')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="All Orders" :items="[['label' => 'Order Management']]" />

        <x-admin.card title="All Orders">
            <x-admin.table id="orders-management-table" :headers="[
                'ID',
                'Order #',
                'Customer',
                'Seller',
                'Status',
                'Total',
                'Date',
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
        const table = $('#orders-management-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.order-management.orders') }}",
            columns: [
                { data: 'order_id', name: 'id' },
                { data: 'order_number', name: 'order_number' },
                { data: 'customer_name', name: 'customer.name', orderable: false },
                { data: 'seller_name', name: 'seller.store_name', orderable: false },
                { data: 'status_badge', name: 'status' },
                { data: 'grand_total_formatted', name: 'grand_total' },
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'action', orderable: false, searchable: false }
            ],
            order: [[6, 'desc']]
        });

        setInterval(function() {
            table.ajax.reload(null, false);
        }, 30000);
    });
</script>
@endpush
