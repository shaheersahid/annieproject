<div id="productImage" class="d-none">
    <div class="row g-3 mb-3 image-row">
        <div class="wrapper d-none">
            <img src="#" alt="Preview" class="rounded border" height="80px" width="80px" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Image</label>
            <input type="file" class="form-control" name="product_images[0][file]" accept="image/*">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Alt Text</label>
            <input type="text" class="form-control" name="product_images[0][alt]" placeholder="Enter alt text">
        </div>
        <div class="col-md-4 d-flex align-items-end gap-2">
            <button type="button" class="btn btn-success add-image"><i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-danger remove-image"><i class="fa fa-minus"></i></button>
        </div>
    </div>
</div>
