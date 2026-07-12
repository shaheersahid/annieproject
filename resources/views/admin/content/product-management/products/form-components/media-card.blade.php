@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? null;
    $mainImage = $isEdit ? $product?->primaryImage()->first() : null;
    $galleryImages = $isEdit ? $product?->images?->where('type', 'gallery') : collect();
@endphp

<div class="card {{ $cardClass ?? '' }}" @if(!empty($stepGroup)) data-step-group="{{ $stepGroup }}" @endif>
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fa fa-images me-2"></i>Product Images</h5>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <label for="thumbnail" class="form-label">Main Thumbnail</label>
            <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*" onchange="ProductForm.handleThumbnailSelect(this)">
            <div class="mt-2" id="thumbnail-preview-container" style="{{ $mainImage ? '' : 'display: none;' }}">
                <div class="position-relative d-inline-block">
                    <img id="thumbnail-preview" src="{{ $mainImage ? $mainImage->url : '' }}" alt="Thumbnail Preview" class="img-thumbnail" style="max-height: 200px;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" id="remove-thumbnail-btn" onclick="ProductForm.removeThumbnail()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <label for="images" class="form-label">Gallery Images <span class="text-muted">{{ $isEdit ? '(Max 9 Total)' : '(Max 9)' }}</span></label>
            <div class="input-group">
                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*" onchange="ProductForm.handleGallerySelect(this)">
                <span class="input-group-text" id="gallery-count">0/9</span>
            </div>
            <small class="text-muted">Images will be appended. Click trash icon to remove.</small>
            <div id="deleted-images-container"></div>
            <div class="row mt-3" id="gallery-preview">
                @if($isEdit)
                    @foreach ($galleryImages as $image)
                        <div class="col-md-3 col-6 mb-3 existing-image-card" data-id="{{ $image->id }}">
                            <div class="card h-100 border position-relative">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="ProductForm.removeExistingImage('{{ $image->path }}', this)" style="z-index: 5;">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <img src="{{ $image->url }}" class="card-img-top" alt="Product Image" style="height: 100px; object-fit: cover;">
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <label for="video" class="form-label">Upload Video</label>
            <input type="file" class="form-control" id="video" name="video" accept="video/*">
            @if($isEdit && $product?->video_path)
                <small class="text-muted">Current video: {{ basename($product->video_path) }}</small>
            @endif
        </div>
    </div>
</div>
