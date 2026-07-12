@extends('admin.layouts.master')
@section('page-title', 'Top Products')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Top Products" :items="[['label' => 'Reports & Analytics']]" />

        <x-admin.card title="Top Products">
            <x-admin.table id="top-products-table" :headers="['ID', 'Product', 'Category', 'Price', 'Seller', 'Status', 'Action']">
                @foreach($products as $product)
                    <tr>
                        <td>#{{ $product->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $product->primaryImage?->url ?? asset('admin/assets/images/placeholder.png') }}" alt="{{ $product->name }}" class="avatar-sm rounded">
                                <div>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    <small class="text-muted">Sold: {{ $product->sold_qty }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->categories->pluck('name')->implode(', ') ?: '-' }}</td>
                        <td>{{ format_price($product->base_price) }}</td>
                        <td>{{ $product->seller?->store_name ?? '-' }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($product->status) }}</span></td>
                        <td>
                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
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
        $('#top-products-table').DataTable({ order: [[0, 'asc']] });
    });
</script>
@endpush
