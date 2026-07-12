@extends('admin.layouts.master')
@section('page-title', 'Inventory Report')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Inventory Report" :items="[['label' => 'Reports'], ['label' => 'Inventory']]" />

        <x-admin.card title="Filters">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Store</label>
                    <select name="store_id" class="form-select">
                        <option value="">All Stores</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ (string) $storeId === (string) $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </form>
        </x-admin.card>

        <div class="row">
            <div class="col-md-4"><x-admin.card title="Current Stock"><h3 class="mb-0">{{ format_qty($totalCurrentStock) }}</h3></x-admin.card></div>
            <div class="col-md-4"><x-admin.card title="Low Stock Products"><h3 class="mb-0">{{ $lowStockCount }}</h3></x-admin.card></div>
            <div class="col-md-4"><x-admin.card title="Batch Inventory Value"><h3 class="mb-0">{{ format_price($inventoryValue) }}</h3></x-admin.card></div>
        </div>

        <x-admin.card title="Current Stock By Product">
            <x-admin.table id="inventory-stock-table" :headers="[
                'Product',
                'SKU',
                'Store',
                'Current Stock',
                'Min Level',
                'Max Level',
                'Reorder Qty'
            ]" />
        </x-admin.card>

        <x-admin.card title="Batch Level Cost and Valuation">
            <x-admin.table id="inventory-batch-valuation-table" :headers="[
                'Product',
                'Store',
                'Batch',
                'Quantity',
                'Unit Cost',
                'Batch Value'
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
        const storeId = "{{ $storeId }}";

        $('#inventory-stock-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.inventory') }}",
                data: function(d) {
                    d.table = 'current-stock';
                    d.store_id = storeId;
                }
            },
            order: [[0, 'asc']],
            columns: [
                { data: 'product_name', name: 'product_name' },
                { data: 'sku', name: 'products.sku' },
                { data: 'store_name', name: 'store_name' },
                { data: 'current_stock_quantity', name: 'store_product.current_stock_quantity', searchable: false },
                { data: 'minimum_stock_level', name: 'store_product.minimum_stock_level', searchable: false },
                { data: 'maximum_stock_level', name: 'store_product.maximum_stock_level', searchable: false },
                { data: 'reorder_quantity', name: 'store_product.reorder_quantity', searchable: false }
            ]
        });

        $('#inventory-batch-valuation-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.inventory') }}",
                data: function(d) {
                    d.table = 'batch-valuation';
                    d.store_id = storeId;
                }
            },
            order: [[5, 'desc']],
            columns: [
                { data: 'product_name', name: 'product_name' },
                { data: 'store_name', name: 'store_name' },
                { data: 'batch_number', name: 'inventory_batches.batch_number' },
                { data: 'quantity', name: 'inventory_batches.quantity', searchable: false },
                { data: 'unit_cost', name: 'inventory_batches.unit_cost', searchable: false },
                { data: 'batch_value', name: 'batch_value', searchable: false }
            ]
        });
    });
</script>
@endpush
