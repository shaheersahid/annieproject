@extends('admin.layouts.master')

@section('page-title', 'Brands')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Brands" :items="[['label' => 'Brands']]" />

        <div class="row">
            <div class="col-12">
                <x-admin.card title="All Brands">
                    <x-slot name="headerActions">
                        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus me-1"></i> Add New Brand
                        </a>
                    </x-slot>

                    <x-admin.table id="brands-datatable" :headers="[
                        'Name',
                        'Slug',
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
            $('#brands-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.brands.index') }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'slug', name: 'slug' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Toggle Status
            $(document).on('change', '.toggle-status', function() {
                var id = $(this).data('id');
                var url = "{{ route('admin.brands.toggle-active', ':id') }}";
                url = url.replace(':id', id);

                $.ajax({
                    url: url,
                    method: 'PATCH',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Status updated successfully');
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong');
                        $('#brands-datatable').DataTable().draw(false);
                    }
                });
            });
        });
    </script>
@endpush
