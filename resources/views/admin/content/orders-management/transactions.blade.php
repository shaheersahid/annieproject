@extends('admin.layouts.master')
@section('page-title', 'Transactions')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Transactions" :items="[['label' => 'Order Management']]" />

        <x-admin.card title="Transactions">
            <x-admin.table id="transactions-table" :headers="[
                'ID',
                'Transaction #',
                'Order #',
                'Status',
                'Amount',
                'Method',
                'Paid At'
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
        $('#transactions-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.order-management.transactions') }}",
            columns: [
                { data: 'transaction_id', name: 'id' },
                { data: 'transaction_number', name: 'transaction_number' },
                { data: 'order_number', name: 'order.order_number', orderable: false },
                { data: 'status_badge', name: 'status' },
                { data: 'amount_formatted', name: 'amount' },
                { data: 'payment_method', name: 'payment_method', defaultContent: '-' },
                { data: 'paid_at_formatted', name: 'paid_at' }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
