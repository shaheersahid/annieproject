@extends('admin.layouts.master')

@section('page-title', 'Customer Loyalty Points')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Loyalty Points Management" :items="[['label' => 'Loyalty Points']]" />

        <div class="row">
            <div class="col-12">
                <x-admin.card title="Customers with Points">
                    <x-admin.table id="customers-points-table" :headers="[
                        'Customer',
                        'Email',
                        'VIP Status',
                        'Current Balance',
                        'Lifetime Earned',
                        'Joined At',
                        'Action'
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
            $('#customers-points-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.customers.points.index') }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'vip_status', name: 'vip_status' },
                    { data: 'points_display', name: 'points_balance' },
                    { data: 'lifetime_points', name: 'points_lifetime_earned' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[3, 'desc']]
            });
        });
    </script>
@endpush
