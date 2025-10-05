<!-- Bulk Update Stock Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-labelledby="bulkUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkUpdateModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Bulk Update Stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkUpdateForm" method="POST" action="{{ route('warehouse.bulk-update-materials-stock') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Update stock quantities for multiple materials at once.
                    </div>

                    <div id="materialsList" class="mb-3">
                        <!-- Materials will be populated here -->
                    </div>

                    <div class="mb-3">
                        <label for="bulkNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="bulkNotes" name="bulk_notes" rows="3"
                            placeholder="Add any notes about this bulk update..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>
                        Update All
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function bulkUpdateStock() {
        const selectedCheckboxes = document.querySelectorAll('.box-checkbox:checked, .bag-checkbox:checked, .prime-bag-checkbox:checked, .flyer-checkbox:checked');

        if (selectedCheckboxes.length === 0) {
            alert('Please select materials to update');
            return;
        }

        // Clear previous materials
        const materialsList = document.getElementById('materialsList');
        materialsList.innerHTML = '';

        // Add each selected material to the form
        selectedCheckboxes.forEach((checkbox, index) => {
            const materialId = checkbox.value;
            const row = checkbox.closest('tr') || checkbox.closest('.card');

            let materialName = 'Unknown Material';
            let currentStock = 0;

            if (row) {
                // Try to get material name and current stock from the row
                const nameElement = row.querySelector('strong') || row.querySelector('.card-title');
                const stockElement = row.querySelector('.badge');

                if (nameElement) {
                    materialName = nameElement.textContent.trim();
                }

                if (stockElement) {
                    const stockText = stockElement.textContent.trim();
                    const stockMatch = stockText.match(/(\d+)/);
                    if (stockMatch) {
                        currentStock = parseInt(stockMatch[1]);
                    }
                }
            }

            // Create input group for this material
            const inputGroup = document.createElement('div');
            inputGroup.className = 'input-group mb-2';
            inputGroup.innerHTML = `
            <span class="input-group-text" style="min-width: 200px;">${materialName}</span>
            <input type="hidden" name="materials[${index}][id]" value="${materialId}">
            <input type="number" class="form-control" name="materials[${index}][stock_quantity]"
                   value="${currentStock}" min="0" required>
        `;

            materialsList.appendChild(inputGroup);
        });

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('bulkUpdateModal'));
        modal.show();
    }
</script>