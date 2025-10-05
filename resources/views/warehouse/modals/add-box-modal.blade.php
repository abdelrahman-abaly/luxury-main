<!-- Add Box Modal -->
<div class="modal fade" id="addBoxModal" tabindex="-1" aria-labelledby="addBoxModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBoxModalLabel">
                    <i class="fas fa-plus me-2"></i>
                    Add New Box
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBoxForm" method="POST" action="{{ route('warehouse.store-box') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="boxName" class="form-label">Box Name *</label>
                                <input type="text" class="form-control" id="boxName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="boxSku" class="form-label">SKU *</label>
                                <input type="text" class="form-control" id="boxSku" name="sku" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="boxSize" class="form-label">Size</label>
                                <select class="form-select" id="boxSize" name="size">
                                    <option value="small">Small</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="large">Large</option>
                                    <option value="xlarge">X-Large</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="boxNormalPrice" class="form-label">Normal Price (EGP) *</label>
                                <input type="number" class="form-control" id="boxNormalPrice" name="normal_price"
                                    step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="boxSalePrice" class="form-label">Sale Price (EGP) *</label>
                                <input type="number" class="form-control" id="boxSalePrice" name="sale_price"
                                    step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="boxStock" class="form-label">Initial Stock *</label>
                                <input type="number" class="form-control" id="boxStock" name="stock_quantity"
                                    min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="boxStatus" class="form-label">Status</label>
                                <select class="form-select" id="boxStatus" name="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Empty column for layout balance -->
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="boxDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="boxDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="boxImage" class="form-label">Image URL</label>
                        <input type="url" class="form-control" id="boxImage" name="image"
                            placeholder="https://example.com/image.jpg">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Add Box
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
