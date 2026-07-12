@extends('admin.layouts.master')
@section('page-title', 'Purchase Report')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Purchase Report" :items="[['label' => 'Reports'], ['label' => 'Purchases']]" />

        <x-admin.card title="Filters">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Store</label>
                    <select name="store_id" class="form-select">
                        <option value="">All Stores</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ (string) $storeId === (string) $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </form>
        </x-admin.card>

        <div class="row">
            <div class="col-md-4"><x-admin.card title="Total Purchase Value"><h3 class="mb-0">{{ format_price($purchaseTotal) }}</h3></x-admin.card></div>
            <div class="col-md-4"><x-admin.card title="Suppliers In Range"><h3 class="mb-0">{{ $supplierCount }}</h3></x-admin.card></div>
            <div class="col-md-4"><x-admin.card title="Tracked Lines"><h3 class="mb-0">{{ $trackedLines }}</h3></x-admin.card></div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <x-admin.card title="Supplier Totals">
                    <x-admin.table id="purchase-supplier-totals-table" :headers="[
                        'Supplier',
                        'Quantity',
                        'Total Amount'
                    ]" />
                </x-admin.card>
            </div>
            <div class="col-lg-6">
                <x-admin.card title="Average Cost Trend">
                    <x-admin.table id="purchase-average-cost-table" :headers="[
                        'Date',
                        'Average Cost',
                        'Total Amount'
                    ]" />
                </x-admin.card>
            </div>
        </div>

        <x-admin.card title="Recent Purchase Lines">
            <x-admin.table id="purchase-line-items-table" :headers="[
                'PO #',
                'Date',
                'Supplier',
                'Product',
                'Ordered',
                'Received',
                'Cost',
                'Subtotal'
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
        const reportFilters = {
            store_id: "{{ $storeId }}",
            date_from: "{{ $dateFrom }}",
            date_to: "{{ $dateTo }}"
        };

        $('#purchase-supplier-totals-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.purchases') }}",
                data: function(d) {
                    d.table = 'supplier-totals';
                    Object.assign(d, reportFilters);
                }
            },
            order: [[2, 'desc']],
            columns: [
                { data: 'supplier_name', name: 'supplier_name' },
                { data: 'total_quantity', name: 'total_quantity', searchable: false },
                { data: 'total_amount', name: 'total_amount', searchable: false }
            ]
        });

        $('#purchase-average-cost-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.purchases') }}",
                data: function(d) {
                    d.table = 'average-cost-trends';
                    Object.assign(d, reportFilters);
                }
            },
            order: [[0, 'asc']],
            columns: [
                { data: 'report_date', name: 'report_date' },
                { data: 'average_cost', name: 'average_cost', searchable: false },
                { data: 'total_amount', name: 'total_amount', searchable: false }
            ]
        });

        $('#purchase-line-items-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.purchases') }}",
                data: function(d) {
                    d.table = 'line-items';
                    Object.assign(d, reportFilters);
                }
            },
            order: [[1, 'desc']],
            columns: [
                { data: 'po_number', name: 'purchase_orders.po_number' },
                { data: 'order_date', name: 'purchase_orders.order_date' },
                { data: 'supplier_name', name: 'supplier_name' },
                { data: 'product_name', name: 'product_name' },
                { data: 'quantity_ordered', name: 'purchase_order_items.quantity_ordered', searchable: false },
                { data: 'quantity_received', name: 'purchase_order_items.quantity_received', searchable: false },
                { data: 'cost_price_per_unit', name: 'purchase_order_items.cost_price_per_unit', searchable: false },
                { data: 'subtotal', name: 'purchase_order_items.subtotal', searchable: false }
            ]
        });
    });
</script>
@endpush
