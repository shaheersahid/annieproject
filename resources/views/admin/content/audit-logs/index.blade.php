@extends('admin.layouts.master')
@section('page-title', 'Audit Logs')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Audit Logs" :items="[['label' => 'Audit Logs']]" />

        <div class="row">
            <div class="col-12">
                <x-admin.card title="Audit Logs">
                    <x-admin.table id="audit-logs-table" :headers="[
                        'Description',
                        'Log',
                        'Event',
                        'Causer',
                        'Subject',
                        'Properties',
                        'Created'
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
        $('#audit-logs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.audit-logs.index') }}",
            columns: [
                { data: 'description', name: 'description' },
                { data: 'log_name', name: 'log_name' },
                { data: 'event', name: 'event' },
                { data: 'causer_name', name: 'causer_name', orderable: false, searchable: false },
                { data: 'subject_name', name: 'subject_name', orderable: false, searchable: false },
                { data: 'properties_summary', name: 'properties', orderable: false, searchable: false },
                { data: 'created_at_human', name: 'created_at', searchable: false }
            ],
            order: [[6, 'desc']]
        });
    });
</script>
@endpush
