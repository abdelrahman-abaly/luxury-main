<!-- Add Shopping Bag Modal -->
<div class="modal fade" id="addBagModal" tabindex="-1" aria-labelledby="addBagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBagModalLabel">
                    <i class="fas fa-plus me-2"></i>
                    Add New Shopping Bag
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBagForm" method="POST" action="{{ route('warehouse.store-shopping-bag') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bagName" class="form-label">Bag Name *</label>
                                <input type="text" class="form-control" id="bagName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bagSku" class="form-label">SKU *</label>
                                <input type="text" class="form-control" id="bagSku" name="sku" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="bagSize" class="form-label">Size</label>
                                <select class="form-select" id="bagSize" name="size">
                                    <option value="small">Small</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="large">Large</option>
                                    <option value="xlarge">X-Large</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="bagColor" class="form-label">Color</label>
                                <input type="text" class="form-control" id="bagColor" name="color"
                                    placeholder="e.g., white, black, blue">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="bagPrice" class="form-label">Price (EGP) *</label>
                                <input type="number" class="form-control" id="bagPrice" name="normal_price"
                                    step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bagSalePrice" class="form-label">Sale Price (EGP) *</label>
                                <input type="number" class="form-control" id="bagSalePrice" name="sale_price"
                                    step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bagStock" class="form-label">Initial Stock *</label>
                                <input type="number" class="form-control" id="bagStock" name="stock_quantity"
                                    min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bagStatus" class="form-label">Status</label>
                                <select class="form-select" id="bagStatus" name="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bagImage" class="form-label">Image URL</label>
                                <input type="url" class="form-control" id="bagImage" name="image"
                                    placeholder="https://example.com/image.jpg">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bagDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="bagDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Add Bag
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
