@extends('admin.layouts.master')
@section('page-title', 'Manage Inventory')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Manage Inventory" :items="[['label' => 'Manage Inventory']]" />

        <x-admin.card title="Manage Inventory">
            <x-admin.table id="manage-inventory-table" :headers="[
                'Product',
                'Category',
                'Stock',
                'Sold Out',
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
        $('#manage-inventory-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.manage-inventory.index') }}",
            columns: [
                { data: 'product_summary', name: 'name' },
                { data: 'category', name: 'category', orderable: false, searchable: false },
                { data: 'stock_badge', name: 'stock', className: 'text-center' },
                { data: 'sold_out_badge', name: 'sold_out', className: 'text-center' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
