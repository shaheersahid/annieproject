<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-light" id="prev-btn" style="display: none;">
                        <i class="fa fa-arrow-left me-1"></i> Previous
                    </button>
                    <div class="ms-auto">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="button" class="btn btn-primary" id="next-btn">
                            Next <i class="fa fa-arrow-right ms-1"></i>
                        </button>
                        <button type="submit" class="btn btn-outline-primary" id="draft-btn" style="display: none;">
                            <i class="fa fa-file-lines me-1"></i> Save Draft
                        </button>
                        <button type="submit" class="btn btn-success" id="submit-btn" style="display: none;">
                            <i class="fa fa-save me-1"></i> {{ $submitLabel ?? 'Save Product' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
