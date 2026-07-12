@extends('admin.layouts.master')
@section('page-title', 'Categories')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <x-admin.breadcrumb title="Categories" :items="[['label' => 'Categories']]" />
           
            <div class="row">
              <div class="col-12">
                 <x-admin.card title="All Categories">
                        <x-slot name="headerActions">
                            @can('create categories')
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus-circle me-1"></i> Add Category
                            </a>
                            @endcan
                        </x-slot>
                <!-- 'Shown on Home', -->
                        <x-admin.table id="categories-table" :headers="[
                                'Name',
                                'Parent Category',
                                'Total Products',
                                'Status',
                                'Created At',
                                'Actions'
                        ]" class="table table-hover table-bordered table-striped dt-responsive nowrap" />
                   </x-admin.card>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('admin-scripts')
    <script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            var table = $('#categories-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.categories.index') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'parentCategory',
                        name: 'parentCategory'
                    },
                    {
                        data: 'totalProducts',
                        name: 'totalProducts'
                    },
                    // {
                    //     data: 'homepage',
                    //     name: 'homepage',
                    //     orderable: false,
                    //     searchable: false,
                    //     className: 'text-center'
                    // },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [[4, 'desc']]
            });

            // Toggle Status/Homepage
            $(document).on('change', '.toggle-status', function() {
                var id = $(this).data('id');
                var type = $(this).data('type');
                var value = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin.categories.toggle-status') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        type: type,
                        value: value
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            table.draw(false);
                        }
                    },
                    error: function(xhr) {
                         let message = 'Something went wrong!';
                         if(xhr.responseJSON && xhr.responseJSON.message) {
                             message = xhr.responseJSON.message;
                         }
                        toastr.error(message);
                        table.draw(false);
                    }
                });
            });

        });
    </script>
@endpush
