@extends('admin.layouts.master')

@section('page-title', 'Sellers')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Sellers" :items="[['label' => 'Sellers']]" />

        <div class="row">
            <div class="col-12">
                <x-admin.card title="Sellers">
                    <x-slot name="headerActions">
                        <a href="{{ route('admin.sellers.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus me-1"></i> Add Seller
                        </a>
                    </x-slot>

                    <x-admin.table id="sellers-table" :headers="[
                        'Store',
                        'Owner',
                        'Phone',
                        'Country',
                        'Location',
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
        $('#sellers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.sellers.index') }}",
            columns: [
                { data: 'store', name: 'store_name' },
                { data: 'contact', name: 'owner_name' },
                { data: 'phone', name: 'phone' },
                { data: 'country', name: 'country' },
                { data: 'location', name: 'location' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('change', '.toggle-status', function () {
            var url = "{{ route('admin.sellers.toggle-active', ':id') }}".replace(':id', $(this).data('id'));

            $.ajax({
                url: url,
                method: 'PATCH',
                data: { _token: "{{ csrf_token() }}" },
                error: function () {
                    $('#sellers-table').DataTable().draw(false);
                }
            });
        });
    });
</script>
@endpush
