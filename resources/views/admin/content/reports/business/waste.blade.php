@extends('admin.layouts.master')
@section('page-title', 'Waste Report')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Waste Report" :items="[['label' => 'Reports'], ['label' => 'Waste']]" />

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
            <div class="col-md-6"><x-admin.card title="Total Wasted Quantity"><h3 class="mb-0">{{ format_qty($totalWastedQuantity) }}</h3></x-admin.card></div>
            <div class="col-md-6"><x-admin.card title="Total Waste Cost"><h3 class="mb-0">{{ format_price($totalWasteCost) }}</h3></x-admin.card></div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <x-admin.card title="Waste By Reason">
                    <x-admin.table id="waste-reason-table" :headers="[
                        'Reason',
                        'Quantity',
                        'Cost'
                    ]" />
                </x-admin.card>
            </div>
            <div class="col-lg-7">
                <x-admin.card title="Waste Entries">
                    <x-admin.table id="waste-entries-table" :headers="[
                        'Date',
                        'Product',
                        'Store',
                        'Supplier',
                        'Reason',
                        'Qty',
                        'Cost'
                    ]" />
                </x-admin.card>
            </div>
        </div>
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

        $('#waste-reason-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.waste') }}",
                data: function(d) {
                    d.table = 'reason-summary';
                    Object.assign(d, reportFilters);
                }
            },
            order: [[2, 'desc']],
            columns: [
                { data: 'reason', name: 'reason' },
                { data: 'total_quantity', name: 'total_quantity', searchable: false },
                { data: 'total_cost', name: 'total_cost', searchable: false }
            ]
        });

        $('#waste-entries-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.waste') }}",
                data: function(d) {
                    d.table = 'entries';
                    Object.assign(d, reportFilters);
                }
            },
            order: [[0, 'desc']],
            columns: [
                { data: 'created_at', name: 'waste_logs.created_at' },
                { data: 'product_name', name: 'product_name' },
                { data: 'store_name', name: 'store_name' },
                { data: 'supplier_name', name: 'supplier_name' },
                { data: 'reason', name: 'waste_logs.reason' },
                { data: 'quantity', name: 'waste_logs.quantity', searchable: false },
                { data: 'total_cost', name: 'total_cost', searchable: false }
            ]
        });
    });
</script>
@endpush
