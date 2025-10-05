<!-- Add Flyer Modal -->
<div class="modal fade" id="addFlyerModal" tabindex="-1" aria-labelledby="addFlyerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="addFlyerModalLabel">
                    <i class="fas fa-file-alt me-2"></i>
                    Add New Flyer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addFlyerForm" method="POST" action="{{ route('warehouse.store-flyer') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-file-alt me-2"></i>
                        <strong>Promotional Material:</strong> Add flyers, brochures, leaflets, or catalogs.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="flyerName" class="form-label">Flyer Name *</label>
                                <input type="text" class="form-control" id="flyerName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="flyerSku" class="form-label">SKU *</label>
                                <input type="text" class="form-control" id="flyerSku" name="sku" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="flyerSize" class="form-label">Size</label>
                                <select class="form-select" id="flyerSize" name="size">
                                    <option value="A3">A3</option>
                                    <option value="A4" selected>A4</option>
                                    <option value="A5">A5</option>
                                    <option value="A6">A6</option>
                                    <option value="BC">Business Card</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="flyerColor" class="form-label">Color</label>
                                <input type="text" class="form-control" id="flyerColor" name="color"
                                    placeholder="e.g., white, cream, colored">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="flyerPrice" class="form-label">Price (EGP) *</label>
                                <input type="number" class="form-control" id="flyerPrice" name="normal_price"
                                    step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="flyerSalePrice" class="form-label">Sale Price (EGP) *</label>
                                <input type="number" class="form-control" id="flyerSalePrice" name="sale_price"
                                    step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="flyerStock" class="form-label">Initial Stock *</label>
                                <input type="number" class="form-control" id="flyerStock" name="stock_quantity"
                                    min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="flyerStatus" class="form-label">Status</label>
                                <select class="form-select" id="flyerStatus" name="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="flyerImage" class="form-label">Image URL</label>
                                <input type="url" class="form-control" id="flyerImage" name="image"
                                    placeholder="https://example.com/image.jpg">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="flyerDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="flyerDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-file-alt me-1"></i>
                        Add Flyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
