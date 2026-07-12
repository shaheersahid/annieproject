@extends('admin.layouts.master')

@section('page-title', 'Tags')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Tags" :items="[['label' => 'Tags']]" />

        <div class="row">
            <div class="col-12">
                <x-admin.card title="Tag List">
                    <x-slot name="headerActions">
                        <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus me-1"></i> Add Tag
                        </a>
                    </x-slot>

                    <x-admin.table id="tags-table" :headers="[
                        'Name',
                        'Slug',
                        'Type',
                        'Option',
                        'Products',
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
        $('#tags-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.attributes.index') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'slug', name: 'slug' },
                { data: 'type_label', name: 'type' },
                { data: 'option_label', name: 'option' },
                { data: 'products_count', name: 'products_count', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('change', '.toggle-status', function () {
            var url = "{{ route('admin.attributes.toggle-active', ':id') }}".replace(':id', $(this).data('id'));

            $.ajax({
                url: url,
                method: 'PATCH',
                data: { _token: "{{ csrf_token() }}" },
                error: function () {
                    $('#tags-table').DataTable().draw(false);
                }
            });
        });
    });
</script>
@endpush
