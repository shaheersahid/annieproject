<div class="modal fade" id="adjustStockModal" tabindex="-1" role="dialog" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustStockModalLabel">Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adjustStockForm" action="{{ route('admin.inventory.adjust') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="adjust_product_id">
                    <input type="hidden" name="store_id" id="adjust_store_id">

                    <div class="mb-3" id="product-select-wrapper" style="display:none;">
                        <label class="form-label">Product</label>
                        <select class="form-select" id="adjust_product_select">
                            <option value="">Select product</option>
                            @if(isset($products))
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-has-variants="{{ $product->has_variants ? 1 : 0 }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3" id="variant-select-wrapper" style="display:none;">
                        <label class="form-label">Variant <span class="text-muted small">(leave blank to adjust product aggregate only)</span></label>
                        <select class="form-select" name="variant_id" id="adjust_variant_id">
                            <option value="">— All / No specific variant —</option>
                        </select>
                        <div id="variant-stock-hint" class="form-text text-muted mt-1"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adjustment Type</label>
                        <select class="form-select" name="type" id="adjust_type" required>
                            <option value="addition">Add Stock (+)</option>
                            <option value="subtraction">Remove Stock (-)</option>
                            <option value="set">Set Quantity (=)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" id="adjust_quantity" min="0.001" step="0.001" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" name="reason" rows="2" placeholder="e.g. Broken item, Inventory correction" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveAdjustmentBtn" data-original-text="Save Changes">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
