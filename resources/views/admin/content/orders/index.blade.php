@extends('admin.layouts.master')
@section('page-title', $title ?? 'Orders')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <x-admin.breadcrumb :title="$title ?? 'Orders'" :items="[['label' => $title ?? 'Orders']]" />
            <div class="row">
                <div class="col-12">
                    <x-admin.card :title="($title ?? 'Orders') . ' ' . (auth()->user()->default_store_id ? '('.auth()->user()->defaultStore->name.')' : '')">
                        <x-slot name="headerActions">
                            @if(!auth()->user()->default_store_id)
                            <div class="input-group" style="width: 250px;">
                                <label class="input-group-text">Store</label>
                                <select class="form-select" id="store-filter">
                                    <option value="">All Stores</option>
                                    @foreach(\App\Models\Store::active()->get() as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </x-slot>
                        <x-admin.table id="orders-table" :headers="[
                            'Order #',
                            'Customer',
                            'Total',
                            'Status',
                            'Payment',
                            'Date',
                            'Actions'
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
    <script type="text/javascript">
        $(function() {
            var table = $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url()->current() }}",
                    data: function(d) {
                        d.store_id = $('#store-filter').val() || "{{ auth()->user()->default_store_id }}";
                        d.channel = "{{ $fulfillmentChannel }}";
                    }
                },
@if(!auth()->user()->default_store_id)
                initComplete: function() {
                    $('#store-filter').on('change', function() {
                        table.draw();
                    });
                },
@endif
                order: [[5, 'desc']],
                columns: [
                    { data: 'display_order_number', name: 'order_number' },
                    { data: 'customer', name: 'user.name' },
                    { data: 'total_amount', name: 'total_amount' },
                    { data: 'status_badge', name: 'status' },
                    { data: 'payment_status_badge', name: 'payment_status' },
                    { data: 'created_at_formatted', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush
