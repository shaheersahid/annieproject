@extends('admin.layouts.master')
@section('page-title', 'Draft Products')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Draft Products" :items="[['label' => 'Product Management', 'url' => route('admin.products.index')], ['label' => 'Drafts']]" />
        
        <x-admin.card title="Draft Products">
            <x-admin.table id="drafts-table" :headers="[
                ['label' => 'Image', 'width' => '60px'],
                ['label' => 'Name', 'maxWidth' => '200px'],
                ['label' => 'SKU', 'width' => '100px'],
                ['label' => 'Categories', 'width' => '150px'],
                ['label' => 'Brand', 'width' => '120px'],
                'Price',
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
        $('#drafts-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.products.drafts') }}",
            columns: [
                { data: 'image',           name: 'image',           orderable: false, searchable: false },
                { data: 'name',            name: 'name' },
                { data: 'sku',             name: 'sku' },
                { data: 'categories',      name: 'categories',      orderable: false, searchable: false },
                { data: 'brand',           name: 'brand',           orderable: false, searchable: false },
                { data: 'price_formatted', name: 'price',           orderable: true },
                { data: 'action',                                   orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
