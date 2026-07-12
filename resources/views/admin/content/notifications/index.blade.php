@extends('admin.layouts.master')
@section('page-title', 'Notifications')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Notifications" :items="[['label' => 'Notifications']]" />
        
        <div class="row">
            <div class="col-12">
                <x-admin.card title="Notifications">
                    <x-slot name="headerActions">
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <a href="{{ route('admin.notifications.markAllRead') }}" class="btn btn-sm btn-primary">
                                <i class="mdi mdi-check-all me-1"></i> Mark All as Read
                            </a>
                        @endif
                    </x-slot>

                    <x-admin.table id="notifications-table" :headers="[
                        'Message',
                        'Received',
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
        $('#notifications-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.notifications.index') }}",
            columns: [
                { data: 'message', name: 'data->message' },
                { data: 'created_at_human', name: 'created_at', searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']] // Sort by Received date desc by default
        });
    });
</script>
@endpush
