<button type="button" class="btn btn-sm btn-soft-info edit-review-btn" 
    data-id="{{ $review->id }}" 
    data-status="{{ $review->status }}" 
    data-review-text="{{ $review->review_text }}" 
    data-reply-text="{{ $review->reply_text }}"
    title="Moderate">
    <i class="mdi mdi-pencil font-size-16"></i> Moderate
</button>
