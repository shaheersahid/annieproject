@extends('admin.layouts.master')
@section('page-title', 'Newsletter Subscribers')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <x-admin.breadcrumb title="Newsletter Subscribers" :items="[['label' => 'Newsletter Subscribers']]" />

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <x-admin.card title="All Subscribers">
                        <x-slot name="headerActions">
                            @can('send newsletter')
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sendNewsletterModal">
                                <i class="fa fa-paper-plane me-1"></i> Send Newsletter
                            </button>
                            @endcan
                        </x-slot>

                        <x-admin.table id="newsletter-table" :headers="[
                            'Email',
                            'Status',
                            'Subscribed At',
                            'Actions'
                        ]" />
                    </x-admin.card>
                </div>
            </div>
        </div>
    </div>

    @can('send newsletter')
    {{-- Send Newsletter Modal --}}
    <div class="modal fade" id="sendNewsletterModal" tabindex="-1" aria-labelledby="sendNewsletterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendNewsletterModalLabel">
                        <i class="fa fa-paper-plane me-2"></i> Send Newsletter
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.newsletter.send') }}" method="POST" id="newsletter-form">
                    @csrf
                    <div class="modal-body">

                        <!-- <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
                            <i class="fa fa-info-circle"></i>
                            <span>
                                Will be sent to <strong>{{ number_format($subscriberCount) }} active subscriber(s)</strong>
                                via background queue.
                            </span>
                        </div> -->

                        @if($subscriberCount === 0)
                            <div class="alert alert-warning mb-3">
                                <i class="fa fa-exclamation-triangle me-1"></i>
                                No active subscribers found. No emails will be sent.
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="subject" class="form-label fw-semibold">
                                Subject <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('subject') is-invalid @enderror"
                                   id="subject"
                                   name="subject"
                                   value="{{ old('subject') }}"
                                   placeholder="e.g. Exclusive Offer This Week!"
                                   maxlength="255"
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label fw-semibold">
                                Message <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('body') is-invalid @enderror"
                                      id="body"
                                      name="body"
                                      rows="10"
                                      maxlength="10000"
                                      placeholder="Write your message here..."
                                      required>{{ old('body') }}</textarea>
                            <div class="form-text d-flex justify-content-between">
                                <span>Plain text. Max 10,000 characters.</span>
                                <span id="char-count" class="text-muted">0 / 10000</span>
                            </div>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirm-send-btn"
                                @if($subscriberCount === 0) disabled @endif>
                            <i class="fa fa-paper-plane me-1"></i> Send to {{ number_format($subscriberCount) }} Subscriber(s)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Confirm Send Modal --}}
    <div class="modal fade" id="confirmSendModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Send</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">You are about to send this newsletter to:</p>
                    <h4 class="text-primary mb-1">{{ number_format($subscriberCount) }} subscriber(s)</h4>
                    <p class="text-muted small mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="final-send-btn">
                        <i class="fa fa-paper-plane me-1"></i> Yes, Send Now
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan

@endsection

@push('admin-scripts')
    <script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('#newsletter-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.newsletter.index') }}",
                columns: [
                    { data: 'email',        name: 'email' },
                    { data: 'status_badge', name: 'status',       orderable: false, searchable: false },
                    { data: 'subscribed_at',name: 'subscribed_at' },
                    { data: 'action',       name: 'action',        orderable: false, searchable: false }
                ],
                order: [[2, 'desc']]
            });

            // Character counter
            $('#body').on('input', function() {
                $('#char-count').text($(this).val().length + ' / 10000');
            });

            // "Send" button validates then opens confirm modal
            $('#confirm-send-btn').on('click', function() {
                const subject = $('#subject').val().trim();
                const body    = $('#body').val().trim();

                if (!subject || !body) {
                    if (!subject) $('#subject').addClass('is-invalid');
                    if (!body)    $('#body').addClass('is-invalid');
                    return;
                }

                $('#subject, #body').removeClass('is-invalid');
                $('#sendNewsletterModal').modal('hide');

                // Small delay so first modal closes before second opens
                setTimeout(function() {
                    $('#confirmSendModal').modal('show');
                }, 300);
            });

            // Final confirm submits the form
            $('#final-send-btn').on('click', function() {
                $('#confirmSendModal').modal('hide');
                $('#newsletter-form').submit();
            });

            // Re-open compose modal if validation errors exist on page load
            @if($errors->any())
                setTimeout(function() {
                    $('#sendNewsletterModal').modal('show');
                }, 300);
            @endif
        });
    </script>
@endpush
