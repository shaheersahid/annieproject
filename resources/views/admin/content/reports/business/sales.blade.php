@extends('admin.layouts.master')
@section('page-title', 'Sales Report')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Sales Report" :items="[['label' => 'Reports'], ['label' => 'Sales']]" />

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
            <div class="col-md-3"><x-admin.card title="Total Sales"><h3 class="mb-0">{{ format_price($salesTotal) }}</h3></x-admin.card></div>
            <div class="col-md-3"><x-admin.card title="POS Sales"><h3 class="mb-0">{{ format_price($posSales) }}</h3></x-admin.card></div>
            <div class="col-md-3"><x-admin.card title="Web Sales"><h3 class="mb-0">{{ format_price($webSales) }}</h3></x-admin.card></div>
            <div class="col-md-3"><x-admin.card title="COGS Snapshot"><h3 class="mb-0">{{ format_price($cogsTotal) }}</h3></x-admin.card></div>
        </div>

        <x-admin.card title="Daily Sales Breakdown">
            <x-admin.table id="sales-breakdown-table" :headers="[
                'Date',
                'Total Sales',
                'POS',
                'Web'
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
        $('#sales-breakdown-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.sales') }}",
                data: function(d) {
                    d.table = 'daily-breakdown';
                    d.store_id = "{{ $storeId }}";
                    d.date_from = "{{ $dateFrom }}";
                    d.date_to = "{{ $dateTo }}";
                }
            },
            order: [[0, 'asc']],
            columns: [
                { data: 'report_date', name: 'report_date' },
                { data: 'sales_total', name: 'sales_total', searchable: false },
                { data: 'pos_total', name: 'pos_total', searchable: false },
                { data: 'web_total', name: 'web_total', searchable: false }
            ]
        });
    });
</script>
@endpush
