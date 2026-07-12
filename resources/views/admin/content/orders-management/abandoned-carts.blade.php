@extends('admin.layouts.master')
@section('page-title', 'Abandoned Cart')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Abandoned Cart" :items="[['label' => 'Order Management']]" />

        <x-admin.card title="Abandoned Cart">
            <x-admin.table id="abandoned-carts-table" :headers="[
                'ID',
                'User',
                'Total',
                'Abandoned At',
                'Recovered At'
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
        $('#abandoned-carts-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.order-management.abandoned-carts') }}",
            columns: [
                { data: 'cart_id', name: 'id' },
                { data: 'user_name', name: 'user.name', orderable: false },
                { data: 'cart_total_formatted', name: 'cart_total' },
                { data: 'abandoned_at_formatted', name: 'abandoned_at' },
                { data: 'recovered_at_formatted', name: 'recovered_at' }
            ],
            order: [[3, 'desc']]
        });
    });
</script>
@endpush
