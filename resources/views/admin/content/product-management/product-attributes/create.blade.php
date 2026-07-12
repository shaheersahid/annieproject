@extends('admin.layouts.master')

@section('page-title', 'Create Product Attribute')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            border-radius: .35rem;
            min-height: 38px;
        }
    </style>
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Create Product Attribute" :items="[
            ['label' => 'Product Attributes', 'url' => route('admin.product-attributes.index')],
            ['label' => 'Create']
        ]" />

        <form action="{{ route('admin.product-attributes.store') }}" method="POST">
            @include('admin.content.product-management.product-attributes.form')
        </form>
    </div>
</div>
@endsection

@push('admin-scripts')
    <script src="{{ asset('admin/assets/libs/select2/js/select2.min.js') }}"></script>
    <script>
        $(function() {
            $('#category_ids').select2({
                placeholder: 'Select Categories',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush
