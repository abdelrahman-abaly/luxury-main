<!-- Add Prime Bag Modal -->
<div class="modal fade" id="addPrimeBagModal" tabindex="-1" aria-labelledby="addPrimeBagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="addPrimeBagModalLabel">
                    <i class="fas fa-crown me-2"></i>
                    Add New Prime Bag
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPrimeBagForm" method="POST" action="{{ route('warehouse.store-prime-bag') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-crown me-2"></i>
                        <strong>Prime Bag:</strong> This is a premium packaging material with special features.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="primeBagName" class="form-label">Prime Bag Name *</label>
                                <input type="text" class="form-control" id="primeBagName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="primeBagSku" class="form-label">SKU *</label>
                                <input type="text" class="form-control" id="primeBagSku" name="sku" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="primeBagSize" class="form-label">Size</label>
                                <select class="form-select" id="primeBagSize" name="size">
                                    <option value="small">Small</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="large">Large</option>
                                    <option value="xlarge">X-Large</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="primeBagColor" class="form-label">Color</label>
                                <input type="text" class="form-control" id="primeBagColor" name="color"
                                    placeholder="e.g., black, brown, navy">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="primeBagPrice" class="form-label">Price (EGP) *</label>
                                <input type="number" class="form-control" id="primeBagPrice" name="normal_price"
                                    step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="primeBagSalePrice" class="form-label">Sale Price (EGP) *</label>
                                <input type="number" class="form-control" id="primeBagSalePrice" name="sale_price"
                                    step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="primeBagStock" class="form-label">Initial Stock *</label>
                                <input type="number" class="form-control" id="primeBagStock" name="stock_quantity"
                                    min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="primeBagStatus" class="form-label">Status</label>
                                <select class="form-select" id="primeBagStatus" name="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="primeBagImage" class="form-label">Image URL</label>
                                <input type="url" class="form-control" id="primeBagImage" name="image"
                                    placeholder="https://example.com/image.jpg">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="primeBagDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="primeBagDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-crown me-1"></i>
                        Add Prime Bag
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
