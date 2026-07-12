@extends('admin.layouts.master')
@section('page-title', 'Category Reports')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Category Reports" :items="[['label' => 'Reports & Analytics']]" />

        <x-admin.card title="Categories by Product Count">
            <x-admin.table id="categories-table" :headers="['ID', 'Category', 'Slug', 'Products', 'Status']">
                @foreach($categories as $category)
                    <tr>
                        <td>#{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td><span class="badge bg-info-subtle text-info">{{ $category->products_count }}</span></td>
                        <td>
                            @if($category->is_active ?? true)
                                <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        </x-admin.card>
    </div>
</div>
@endsection

@push('admin-scripts')
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(function() {
        $('#categories-table').DataTable({ order: [[3, 'desc']] });
    });
</script>
@endpush
