@extends('admin.layouts.master')
@section('page-title', 'Contact Submission Details')

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Contact Submission #{{ $contact->id }}</h4>
                            <a href="{{ route('admin.contact.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5>Contact Information</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Name:</th>
                                            <td>{{ $contact->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td><a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a></td>
                                        </tr>
                                        <tr>
                                            <th>Subject:</th>
                                            <td>{{ $contact->subject }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Submission Details</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Status:</th>
                                            <td>
                                                <select class="form-select" id="status-select">
                                                    <option value="new" {{ $contact->status == 'new' ? 'selected' : '' }}>New</option>
                                                    <option value="read" {{ $contact->status == 'read' ? 'selected' : '' }}>Read</option>
                                                    <option value="replied" {{ $contact->status == 'replied' ? 'selected' : '' }}>Replied</option>
                                                    <option value="archived" {{ $contact->status == 'archived' ? 'selected' : '' }}>Archived</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Submitted At:</th>
                                            <td>{{ $contact->created_at->format('d M Y, H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Read At:</th>
                                            <td>{{ $contact->read_at ? $contact->read_at->format('d M Y, H:i') : 'Not yet read' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <h5>Message</h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <p class="mb-0">{{ $contact->message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('admin-scripts')
    <script type="text/javascript">
        $(function() {
            $('#status-select').on('change', function() {
                var status = $(this).val();
                $.ajax({
                    url: "{{ route('admin.contact.updateStatus', $contact->id) }}",
                    method: 'PATCH',
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to update status.');
                    }
                });
            });
        });
    </script>
@endpush
