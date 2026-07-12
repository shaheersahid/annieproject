@extends('admin.layouts.master')
@section('page-title', 'Stock Products')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Stock Products" :items="[['label' => 'Product Management', 'url' => route('admin.products.index')], ['label' => 'Stock Products']]" />

        <div class="row">
            <div class="col-md-3">
                <x-admin.card>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-2">Total Products</p>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        </div>
                    </div>
                </x-admin.card>
            </div>
            <div class="col-md-3">
                <x-admin.card>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-2">In Stock (>10)</p>
                            <h4 class="mb-0 text-success">{{ $stats['in_stock'] }}</h4>
                        </div>
                    </div>
                </x-admin.card>
            </div>
            <div class="col-md-3">
                <x-admin.card>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-2">Low Stock (1-10)</p>
                            <h4 class="mb-0 text-warning">{{ $stats['low_stock'] }}</h4>
                        </div>
                    </div>
                </x-admin.card>
            </div>
            <div class="col-md-3">
                <x-admin.card>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-2">Out of Stock</p>
                            <h4 class="mb-0 text-danger">{{ $stats['out_of_stock'] }}</h4>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>
        
        <x-admin.card title="Inventory Levels">
            <x-slot name="headerActions">
                <a href="{{ route('admin.products.stocks.add-stock.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Add Stock
                </a>
            </x-slot>

            <x-admin.table id="stock-products-table" :headers="[
                ['label' => 'Product', 'maxWidth' => '260px'],
                ['label' => 'Brand', 'width' => '120px'],
                ['label' => 'Stock Level'],
                ['label' => 'Status'],
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
        const table = $('#stock-products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.products.stock-products') }}",
            columns: [
                { data: 'product_summary', name: 'name' },
                { data: 'brand',           name: 'brand',           orderable: false, searchable: false },
                { data: 'stock_badge',     name: 'stock',           className: 'text-center' },
                { data: 'status_badge',    name: 'status',          className: 'text-center' },
                { data: 'action',                                   orderable: false, searchable: false }
            ]
        });

        setInterval(function() {
            table.ajax.reload(null, false);
        }, 30000);
    });
</script>
@endpush
