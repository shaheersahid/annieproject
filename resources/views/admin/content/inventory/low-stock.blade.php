@extends('admin.layouts.master')
@section('page-title', 'Low Stock')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Low Stock" :items="[['label' => 'Inventory'], ['label' => 'Low Stock']]" />

        <div class="row">
            <div class="col-12">
                <x-admin.card title="Low Stock Products">
                    <x-slot name="headerActions">
                        @if(!auth()->user()->default_store_id)
                        <div class="input-group" style="width: 250px;">
                            <label class="input-group-text" for="store-select">Store</label>
                            <select class="form-select" id="store-select">
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ (string) $storeId === (string) $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @else
                            <input type="hidden" id="store-select" value="{{ auth()->user()->default_store_id }}">
                        @endif
                    </x-slot>

                    <x-admin.table id="low-stock-table" :headers="[
                        'Product',
                        'SKU',
                        'Current Stock',
                        'Min Level',
                        'Reorder Qty',
                        'Action'
                    ]" />
                </x-admin.card>
            </div>
        </div>
    </div>
</div>
@endsection

@include('admin.content.inventory.adjust-modal')

@push('admin-scripts')
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(function() {
        const table = $('#low-stock-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.inventory.lowStock') }}",
                data: function(d) {
                    d.store_id = $('#store-select').val() || "{{ auth()->user()->default_store_id }}";
                }
            },
            order: [[2, 'asc']],
            columns: [
                { data: 'name', name: 'products.name' },
                { data: 'sku', name: 'products.sku' },
                { data: 'current_stock_quantity', name: 'store_product.current_stock_quantity', searchable: false },
                { data: 'minimum_stock_level', name: 'store_product.minimum_stock_level', searchable: false },
                { data: 'reorder_qty', name: 'reorder_qty', searchable: false, orderable: false },
                { data: 'action', orderable: false, searchable: false }
            ]
        });

        $('#store-select').on('change', function() {
            table.draw();
        });

        $('#low-stock-table').on('click', '.adjust-stock-btn', function() {
            let btn = $(this);
            let productId = btn.data('id');
            let hasVariants = btn.data('has-variants') == 1 || btn.data('has-variants') === true;
            let storeId = $('#store-select').val();

            $('#adjust_product_id').val(productId);
            $('#adjust_store_id').val(storeId);
            $('#adjustStockForm')[0].reset();
            $('#variant-select-wrapper').hide();
            $('#adjust_variant_id').html('<option value="">— All / No specific variant —</option>');
            $('#variant-stock-hint').text('');

            if (hasVariants) {
                $.getJSON("{{ route('admin.inventory.variants', ['product' => '__ID__']) }}".replace('__ID__', productId), { store_id: storeId }, function(data) {
                    if (data.variants && data.variants.length > 0) {
                        data.variants.forEach(function(v) {
                            $('#adjust_variant_id').append(
                                $('<option>', { value: v.id, text: v.label + ' (SKU: ' + v.sku + ', Stock: ' + v.stock_formatted + ')' })
                            );
                        });
                        $('#variant-select-wrapper').show();
                    }
                });
            }

            $('#adjustStockModal').modal('show');
        });

        $('#adjust_variant_id').on('change', function() {
            let selected = $(this).find('option:selected');
            let text = selected.val() ? selected.text() : '';
            let match = text.match(/Stock: ([\d.]+)/);
            $('#variant-stock-hint').text(match ? 'Current variant stock at selected store: ' + match[1] : '');
        });

        $('#adjustStockForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = $('#saveAdjustmentBtn');
            let originalText = btn.data('original-text');

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    $('#adjustStockModal').modal('hide');
                    table.draw(false);
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors;
                    let errorMessage = 'Something went wrong.';
                    if (errors) {
                        errorMessage = Object.values(errors).flat().join('<br>');
                    } else if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endpush
