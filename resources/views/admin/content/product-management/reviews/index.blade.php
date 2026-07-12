@extends('admin.layouts.master')
@section('page-title', 'Product Reviews')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Product Reviews" :items="[['label' => 'Product Management', 'url' => route('admin.products.index')], ['label' => 'Reviews']]" />
        
        <x-admin.card title="Customer Reviews">
            <x-admin.table id="reviews-table" :headers="[
                ['label' => 'Product'],
                ['label' => 'Customer', 'width' => '150px'],
                ['label' => 'Rating', 'width' => '120px'],
                ['label' => 'Status', 'width' => '100px'],
                ['label' => 'Date', 'width' => '120px'],
                'Action'
            ]" />
        </x-admin.card>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="reviewForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Moderate Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Customer Review</label>
                        <p id="reviewText" class="p-2 bg-light rounded"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="reviewStatus" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Admin Reply (Optional)</label>
                        <textarea name="reply_text" id="replyText" class="form-control" rows="4" placeholder="Write a reply to the customer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('admin-scripts')
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(function() {
        let table = $('#reviews-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.product-review.index') }}",
            columns: [
                { data: 'product',         name: 'product.name' },
                { data: 'reviewer',        name: 'reviewer.name' },
                { data: 'rating_stars',    name: 'rating',          searchable: false },
                { data: 'status_badge',    name: 'status',          className: 'text-center' },
                { data: 'created_at',      name: 'created_at' },
                { data: 'action',                                   orderable: false, searchable: false }
            ]
        });

        const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));

        $(document).on('click', '.edit-review-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const reviewText = $(this).data('review-text');
            const replyText = $(this).data('reply-text');
            const status = $(this).data('status');
            
            $('#reviewForm').attr('action', '/admin/product-review/' + id);
            $('#reviewText').text(reviewText || 'No text provided.');
            $('#replyText').val(replyText || '');
            $('#reviewStatus').val(status);
            
            reviewModal.show();
        });

        $('#reviewForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        reviewModal.hide();
                        table.draw();
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Something went wrong.');
                }
            });
        });
    });
</script>
@endpush
