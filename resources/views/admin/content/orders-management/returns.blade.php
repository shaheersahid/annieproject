@extends('admin.layouts.master')
@section('page-title', 'Return & Refund')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Return & Refund" :items="[['label' => 'Order Management']]" />

        <x-admin.card title="Return & Refund">
            <x-admin.table id="returns-table" :headers="[
                'ID',
                'Return #',
                'Order #',
                'Status',
                'Refund Amount',
                'Requested'
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
        $('#returns-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.order-management.returns-refunds') }}",
            columns: [
                { data: 'return_id', name: 'id' },
                { data: 'return_number', name: 'return_number' },
                { data: 'order_number', name: 'order.order_number', orderable: false },
                { data: 'status_badge', name: 'status' },
                { data: 'refund_amount_formatted', name: 'refund_amount' },
                { data: 'requested_at_formatted', name: 'requested_at' }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
