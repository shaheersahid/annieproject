@extends('admin.layouts.master')
@section('page-title', 'Profit & Loss')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Profit & Loss" :items="[['label' => 'Reports'], ['label' => 'Profit & Loss']]" />

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
            <div class="col-md-4"><x-admin.card title="Revenue"><h3 class="mb-0">{{ format_price($sales) }}</h3></x-admin.card></div>
            <div class="col-md-4"><x-admin.card title="COGS"><h3 class="mb-0">{{ format_price($cogs) }}</h3></x-admin.card></div>
            <div class="col-md-4"><x-admin.card title="Gross Profit"><h3 class="mb-0">{{ format_price($grossProfit) }}</h3></x-admin.card></div>
        </div>

        <div class="row">
            <div class="col-md-4"><x-admin.card title="Expenses"><h3 class="mb-0">{{ format_price($expenses) }}</h3></x-admin.card></div>
            <div class="col-md-4"><x-admin.card title="Waste"><h3 class="mb-0">{{ format_price($waste) }}</h3></x-admin.card></div>
            <div class="col-md-4"><x-admin.card title="Net Profit"><h3 class="mb-0 {{ $netProfit < 0 ? 'text-danger' : 'text-success' }}">{{ format_price($netProfit) }}</h3></x-admin.card></div>
        </div>

        <x-admin.card title="Profit Breakdown">
            <x-admin.table id="profit-loss-breakdown-table" :headers="[
                'Date From',
                'Date To',
                'Revenue',
                'COGS',
                'Gross Profit',
                'Expenses',
                'Waste',
                'Net Profit'
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
        $('#profit-loss-breakdown-table').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            info: false,
            ajax: {
                url: "{{ route('admin.reports.profit-loss') }}",
                data: function(d) {
                    d.table = 'breakdown';
                    d.store_id = "{{ $storeId }}";
                    d.date_from = "{{ $dateFrom }}";
                    d.date_to = "{{ $dateTo }}";
                }
            },
            ordering: false,
            columns: [
                { data: 'date_from', name: 'date_from' },
                { data: 'date_to', name: 'date_to' },
                { data: 'revenue', name: 'revenue' },
                { data: 'cogs', name: 'cogs' },
                { data: 'gross_profit', name: 'gross_profit' },
                { data: 'expenses', name: 'expenses' },
                { data: 'waste', name: 'waste' },
                { data: 'net_profit', name: 'net_profit' }
            ]
        });
    });
</script>
@endpush
