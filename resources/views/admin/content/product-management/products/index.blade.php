@extends('admin.layouts.master')
@section('page-title', 'Products')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Products" :items="[['label' => 'Products']]" />

        <div class="row">
            <div class="col-12">
                <x-admin.card title="All Products">
                    <x-slot name="headerActions">
                        <div class="input-group" style="width: 200px;" id="stock-filter-wrapper">
                            <label class="input-group-text">Stock</label>
                            <select class="form-select" id="stock-filter">
                                <option value="">All Stock</option>
                                <option value="in_stock">In Stock</option>
                                <option value="low_stock">Low Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item export-btn" href="#" data-format="csv"><i class="fas fa-file-csv me-2 text-success"></i> CSV</a></li>
                                <li><a class="dropdown-item export-btn" href="#" data-format="excel"><i class="fas fa-file-excel me-2 text-success"></i> Excel</a></li>
                            </ul>
                        </div>
                        @can('admin.products.create')
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus-circle me-1"></i> Add New Product
                            </a>
                        @endcan
                    </x-slot>

                    <x-admin.table id="products-table" :headers="[
                        ['label' => 'ID'],
                        ['label' => 'Product', 'maxWidth' => '260px'],
                        ['label' => 'Category', 'width' => '180px'],
                        'Price',
                        ['label' => 'Seller', 'width' => '160px'],
                        'Status',
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
<script>
    $(function() {


        let table = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.products.index') }}",
                data: function(d) {
                    d.stock_status = $('#stock-filter').val();
                }
            },
            order: [[0, 'desc']],
            columns: [
                { data: 'id',              name: 'id',              visible: false, searchable: false },
                { data: 'product_summary', name: 'name' },
                { data: 'category',        name: 'category',        orderable: false, searchable: false },
                { data: 'price_formatted', name: 'base_price',      orderable: true },
                { data: 'seller',          name: 'seller.store_name', orderable: false },
                { data: 'status',          name: 'status',          className: 'text-center' },
                { data: 'action',                                   orderable: false, searchable: false }
            ]
        });

        setInterval(function() {
            table.ajax.reload(null, false);
        }, 30000);

        $('#stock-filter').on('change', function() {
            table.draw();
        });

        $(document).on('change', '.toggle-product-status', function() {
            const $switch = $(this);
            $.ajax({
                url: "{{ route('admin.products.update-status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: $switch.data('id'),
                    is_active: $switch.is(':checked') ? 1 : 0
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload(null, false);
                    }
                },
                error: function() {
                    $switch.prop('checked', !$switch.is(':checked'));
                    toastr.error('Failed to update status.');
                }
            });
        });

        // Handle Export
        $('.export-btn').on('click', function(e) {
            e.preventDefault();
            const format = $(this).data('format');
            const status = $('#stock-filter').val() || '';

            let url = "{{ route('admin.products.export') }}?format=" + format;
            if (status) url += "&status=" + status;

            window.location.href = url;
        });
    });

</script>
@endpush
