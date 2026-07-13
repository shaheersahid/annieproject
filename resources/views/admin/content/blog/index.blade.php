@extends('admin.layouts.master')
@section('page-title', 'Blog Posts')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <x-admin.breadcrumb title="Blog Posts" :items="[['label' => 'Blog']]" />

            <div class="row">
                <div class="col-12">
                    <x-admin.card title="All Posts">
                        <x-slot name="headerActions">
                            <a href="{{ route('admin.blog.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus-circle me-1"></i> Write Post
                            </a>
                        </x-slot>

                        <x-admin.table id="blog-table" :headers="[
                            'Title',
                            'Author',
                            'Status',
                            'Published',
                            'Created At',
                            'Actions',
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
    <script>
        $(function () {
            var table = $('#blog-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.blog.index') }}",
                columns: [
                    { data: 'title',        name: 'title' },
                    { data: 'author_name',  name: 'author_name', orderable: false },
                    { data: 'status_badge', name: 'status', className: 'text-center' },
                    { data: 'published_at', name: 'published_at' },
                    { data: 'created_at',   name: 'created_at', visible: false, searchable: false },
                    { data: 'action',       name: 'action', orderable: false, searchable: false },
                ],
                order: [[4, 'desc']]
            });
        });
    </script>
@endpush
