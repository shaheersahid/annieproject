@extends('admin.layouts.master')
@section('page-title', 'Stock Movements')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Stock Movements" :items="[['label' => 'Inventory', 'url' => route('admin.inventory.index')], ['label' => 'Stock Movements', 'route' => route('admin.inventory.stockMovements')]]" />

         <div class="row">
            <div class="col-12">
                <x-admin.card title="Stock Movements {{ auth()->user()->default_store_id ? '('.auth()->user()->defaultStore->name.')' : '' }}">
                    <x-slot name="headerActions">
                        @if(!auth()->user()->default_store_id)
                        <div class="input-group" style="width: 250px;">
                            <label class="input-group-text">Store</label>
                            <select class="form-select" id="store-filter">
                                <option value="">All Stores</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                            <input type="hidden" id="store-filter" value="{{ auth()->user()->default_store_id }}">
                        @endif

                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Inventory
                        </a>
                    </x-slot>

                    <x-admin.table id="movements-table" :headers="[
                        'Date',
                        'Product',
                        'Store',
                        'Type',
                        'Quantity',
                        'Performed By',
                        'Reason/Ref'
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
        let table = $('#movements-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.inventory.stockMovements') }}",
                data: function(d) {
                    d.store_id = $('#store-filter').val() || "{{ auth()->user()->default_store_id }}";
                }
            },
            columns: [
                { data: 'created_at', name: 'created_at', render: function(d) { return new Date(d).toLocaleString(); } },
                { data: 'product_name', name: 'product.name' },
                { data: 'store_name', name: 'store.name' },
                { data: 'type_badge', name: 'type' },
                { data: 'quantity', name: 'quantity' },
                { data: 'performed_by_name', name: 'performedBy.name' },
                { data: 'reason_reference', name: 'notes' }
            ],
            order: [[0, 'desc']]
        });

        $('#store-filter').on('change', function() {
            table.draw();
        });
    });
</script>
@endpush
