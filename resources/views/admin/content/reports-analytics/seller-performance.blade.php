@extends('admin.layouts.master')
@section('page-title', 'Seller Performance')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Seller Performance" :items="[['label' => 'Reports & Analytics']]" />

        <x-admin.card title="Seller Performance">
            <x-admin.table id="seller-performance-table" :headers="['ID', 'Seller', 'Owner', 'Orders', 'Total Sales', 'Average Order', 'Status']">
                @foreach($sellers as $seller)
                    <tr>
                        <td>#{{ $seller->id }}</td>
                        <td>{{ $seller->store_name }}</td>
                        <td>{{ $seller->owner_name }}</td>
                        <td>{{ $seller->orders_count }}</td>
                        <td>{{ format_price($seller->total_sales) }}</td>
                        <td>{{ format_price($seller->average_order) }}</td>
                        <td><span class="badge bg-{{ $seller->is_active ? 'success' : 'secondary' }}">{{ $seller->is_active ? 'Active' : 'Inactive' }}</span></td>
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
        $('#seller-performance-table').DataTable({ order: [[4, 'desc']] });
    });
</script>
@endpush
