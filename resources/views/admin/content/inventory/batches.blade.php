@extends('admin.layouts.master')
@section('page-title', 'Batch Management')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Batch Management" :items="[['label' => 'Inventory'], ['label' => 'Batches']]" />

        <div class="row">
            <div class="col-12">
                <x-admin.card title="Retail Inventory Batches">
                    <x-slot name="headerActions">
                        <div class="d-flex gap-2">
                            @if(!auth()->user()->default_store_id)
                            <div class="input-group" style="width: 250px;">
                                <label class="input-group-text" for="batch-store-select">Store</label>
                                <select class="form-select" id="batch-store-select">
                                    <option value="">All Stores</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ (string) $storeId === (string) $store->id ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                                <input type="hidden" id="batch-store-select" value="{{ auth()->user()->default_store_id }}">
                            @endif

                            <div class="input-group" style="width: 220px;">
                                <label class="input-group-text" for="batch-status-select">Status</label>
                                <select class="form-select" id="batch-status-select">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $batchStatus)
                                        <option value="{{ $batchStatus }}" {{ (string) $status === (string) $batchStatus ? 'selected' : '' }}>
                                            {{ ucfirst($batchStatus) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </x-slot>

                    <x-admin.table id="batches-table" :headers="[
                        'Batch #',
                        'Product',
                        'Variant',
                        'Store',
                        'Supplier',
                        'Qty',
                        'Initial Qty',
                        'Unit Cost',
                        'Expiry',
                        'Received',
                        'Status'
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
        const table = $('#batches-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.inventory.batches') }}",
                data: function(d) {
                    d.store_id = $('#batch-store-select').val() || "{{ auth()->user()->default_store_id }}";
                    d.status = $('#batch-status-select').val();
                }
            },
            order: [[9, 'desc']],
            columns: [
                { data: 'batch_number', name: 'batch_number' },
                { data: 'product_name', orderable: false, searchable: false },
                { data: 'variant_name', orderable: false, searchable: false },
                { data: 'store_name', orderable: false, searchable: false },
                { data: 'supplier_name', orderable: false, searchable: false },
                { data: 'quantity', name: 'quantity', searchable: false },
                { data: 'initial_quantity', name: 'initial_quantity', searchable: false },
                { data: 'unit_cost', name: 'unit_cost', searchable: false },
                { data: 'expiry_date', name: 'expiry_date' },
                { data: 'received_date', name: 'received_date' },
                { data: 'status', name: 'status' }
            ]
        });

        $('#batch-store-select, #batch-status-select').on('change', function() {
            table.draw();
        });
    });
</script>
@endpush
