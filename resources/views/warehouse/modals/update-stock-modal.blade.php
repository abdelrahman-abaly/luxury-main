<!-- Update Stock Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1" aria-labelledby="updateStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStockModalLabel">
                    <i class="fas fa-warehouse me-2"></i>
                    Update Stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStockForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="materialName" class="form-label">Material Name</label>
                        <input type="text" class="form-control" id="materialName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="currentStock" class="form-label">Current Stock</label>
                        <input type="number" class="form-control" id="currentStock" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="newStock" class="form-label">New Stock Quantity</label>
                        <input type="number" class="form-control" id="newStock" name="stock_quantity"
                            min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="stockNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="stockNotes" name="notes" rows="3"
                            placeholder="Add any notes about this stock update..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>
                        Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateStock(materialId, currentStock) {
        // Set the form action URL
        document.getElementById('updateStockForm').action = `/warehouse/update-material-stock/${materialId}`;

        // Get material details (you might want to fetch this via AJAX)
        // For now, we'll use the current stock value
        document.getElementById('currentStock').value = currentStock;
        document.getElementById('newStock').value = currentStock;
        document.getElementById('newStock').focus();

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('updateStockModal'));
        modal.show();
    }
</script>