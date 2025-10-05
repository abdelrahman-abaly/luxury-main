<!-- Edit Box Modal -->
<div class="modal fade" id="editBoxModal" tabindex="-1" aria-labelledby="editBoxModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBoxModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Edit Box
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBoxForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editBoxName" class="form-label">Box Name *</label>
                                <input type="text" class="form-control" id="editBoxName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editBoxSku" class="form-label">SKU *</label>
                                <input type="text" class="form-control" id="editBoxSku" name="sku" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editBoxSize" class="form-label">Size</label>
                                <select class="form-select" id="editBoxSize" name="size">
                                    <option value="small">Small</option>
                                    <option value="medium">Medium</option>
                                    <option value="large">Large</option>
                                    <option value="xlarge">X-Large</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editBoxNormalPrice" class="form-label">Normal Price (EGP) *</label>
                                <input type="number" class="form-control" id="editBoxNormalPrice" name="normal_price"
                                    step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editBoxSalePrice" class="form-label">Sale Price (EGP) *</label>
                                <input type="number" class="form-control" id="editBoxSalePrice" name="sale_price"
                                    step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editBoxStock" class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control" id="editBoxStock" name="stock_quantity"
                                    min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editBoxStatus" class="form-label">Status</label>
                                <select class="form-select" id="editBoxStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Empty column for layout balance -->
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editBoxDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editBoxDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="editBoxImage" class="form-label">Image URL</label>
                        <input type="url" class="form-control" id="editBoxImage" name="image"
                            placeholder="https://example.com/image.jpg">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Update Box
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editBox(boxId) {
        // Implementation for editing box
        console.log('Edit box:', boxId);
        // You would typically fetch the box data via AJAX and populate the form
        const modal = new bootstrap.Modal(document.getElementById('editBoxModal'));
        modal.show();
    }
</script>
