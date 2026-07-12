@extends('admin.layouts.master')

@section('page-title', 'Product Attributes')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Product Attributes" :items="[['label' => 'Product Attributes']]" />

        <div class="row">
            <div class="col-12">
                <x-admin.card title="Product Attributes">
                    <x-slot name="headerActions">
                        <a href="{{ route('admin.product-attributes.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus me-1"></i> Add Attribute
                        </a>
                    </x-slot>

                    <x-admin.table id="product-attributes-table" :headers="[
                        'Name',
                        'Type',
                        'Categories',
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
    $(function () {
        $('#product-attributes-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.product-attributes.index') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'input_type_label', name: 'input_type' },
                { data: 'categories_list', name: 'categories_list', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('change', '.toggle-status', function () {
            var url = "{{ route('admin.product-attributes.toggle-active', ':id') }}".replace(':id', $(this).data('id'));

            $.ajax({
                url: url,
                method: 'PATCH',
                data: { _token: "{{ csrf_token() }}" },
                error: function () {
                    $('#product-attributes-table').DataTable().draw(false);
                }
            });
        });
    });
</script>
@endpush
