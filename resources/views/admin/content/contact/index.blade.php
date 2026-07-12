@extends('admin.layouts.master')
@section('page-title', 'Contact Submissions')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <x-admin.breadcrumb title="Contact Submissions" :items="[['label' => 'Contact Submissions']]" />
                
            <div class="row">
                <div class="col-12">
                    <x-admin.card title="All Contacts">
                        <x-admin.table id="contact-table" :headers="[
                            'Name', 
                            'Email', 
                            'Phone', 
                            'Subject', 
                            'Status', 
                            'Submitted At', 
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
    <script type="text/javascript">
        $(function() {
            var table = $('#contact-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.contact.index') }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'subject', name: 'subject' },
                    { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[5, 'desc']]
            });

            // Delete contact
            $(document).on('click', '.delete-contact', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this contact submission?')) {
                    $.ajax({
                        url: "{{ route('admin.contact.index') }}/" + id,
                        method: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                table.draw(false);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to delete contact submission.');
                        }
                    });
                }
            });
        });
    </script>
@endpush
