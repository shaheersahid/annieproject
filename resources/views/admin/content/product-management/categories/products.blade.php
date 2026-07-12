@extends('admin.layouts.master')
@section('page-title', 'Category Products: ' . $category->name)

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-rowreorder-bs5/css/rowReorder.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #category-products-table tbody tr { cursor: move; }
        .reorder-handle { cursor: move; color: #556ee6; font-size: 18px; }
        .dragging { background-color: #f8f9fa !important; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Sort Products: {{ $category->name }}</h4>
                            <p class="text-muted mb-0">Drag and drop rows to reorder products in this category.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Back to Categories
                            </a>
                            <button id="save-order" class="btn btn-primary d-none">
                                <i class="fa fa-save me-1"></i> Save New Order
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="category-products-table" class="table table-hover table-bordered table-striped nowrap" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th style="width: 50px;">Image</th>
                                    <th>Product Details</th>
                                    <th>Price</th>
                                    <th>Current Order</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-rowreorder/js/dataTables.rowReorder.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-rowreorder-bs5/js/rowReorder.bootstrap5.min.js') }}"></script>

<script>
$(function() {
    var table = $('#category-products-table').DataTable({
        processing: true,
        serverSide: false, // Essential for RowReorder to be stable
        ajax: "{{ route('admin.categories.products', $category) }}",
        rowReorder: {
            selector: 'td.reorder-handle',
            dataSrc: 'sort_order',
            update: true
        },
        columns: [
            { 
                data: 'sort_order', 
                name: 'sort_order', 
                className: 'reorder-handle text-center',
                render: function() { return '<i class="fa fa-grip-vertical"></i>'; }
            },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'details', name: 'name' },
            { data: 'price', name: 'price' },
            { data: 'sort_order', name: 'sort_order', className: 'text-center' }
        ],
        order: [[4, 'asc']],
        pageLength: 50
    });

    // Show save button when order changes
    table.on('row-reorder', function(e, diff, edit) {
        if (diff.length > 0) {
            $('#save-order').removeClass('d-none').addClass('animate__animated animate__fadeIn');
        }
    });

    $('#save-order').on('click', function() {
        var btn = $(this);
        var newOrder = [];
        
        // Accurate sequence capture from the current DOM state of the table
        $('#category-products-table tbody tr').each(function(index) {
            var rowData = table.row(this).data();
            if (rowData) {
                newOrder.push({
                    id: rowData.id,
                    position: index + 1
                });
            }
        });

        if (newOrder.length === 0) return;

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('admin.products.reorder') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                order: newOrder
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Order saved successfully!');
                    btn.addClass('d-none');
                    // We don't reload completely to avoid UI jump, but sync internal data
                    table.ajax.reload(null, false);
                }
            },
            error: function() {
                toastr.error('Failed to save order. Please refresh and try again.');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save New Order');
            }
        });
    });
});
</script>
@endpush
