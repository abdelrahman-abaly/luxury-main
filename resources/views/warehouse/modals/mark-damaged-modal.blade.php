<!-- Mark as Damaged Modal -->
<div class="modal fade" id="markDamagedModal" tabindex="-1" aria-labelledby="markDamagedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="markDamagedModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Mark Material as Damaged
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="markDamagedForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Material Selection -->
                        <div class="col-md-12">
                            <label for="material_id" class="form-label">Select Material</label>
                            <select class="form-select" id="material_id" name="material_id" required>
                                <option value="">Choose material...</option>
                                @foreach($materials ?? [] as $material)
                                <option value="{{ $material->id }}">{{ $material->name }} (SKU: {{ $material->sku }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Damage Type -->
                        <div class="col-md-6">
                            <label for="damage_type" class="form-label">Damage Type</label>
                            <select class="form-select" id="damage_type" name="damage_type" required>
                                <option value="">Select type...</option>
                                <option value="torn">Torn/Ripped</option>
                                <option value="crushed">Crushed/Deformed</option>
                                <option value="water">Water Damage</option>
                                <option value="defective">Defective</option>
                                <option value="expired">Expired</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Severity -->
                        <div class="col-md-6">
                            <label for="severity" class="form-label">Severity</label>
                            <select class="form-select" id="severity" name="severity" required>
                                <option value="">Select severity...</option>
                                <option value="minor">Minor - Can be repaired</option>
                                <option value="moderate">Moderate - Partial damage</option>
                                <option value="severe">Severe - Complete loss</option>
                            </select>
                        </div>

                        <!-- Damage Quantity -->
                        <div class="col-md-6">
                            <label for="damage_quantity" class="form-label">Damaged Quantity</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="damage_quantity" name="damage_quantity"
                                    min="1" required>
                                <span class="input-group-text">units</span>
                            </div>
                        </div>

                        <!-- Current Stock -->
                        <div class="col-md-6">
                            <label for="current_stock" class="form-label">Current Stock</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="current_stock" readonly>
                                <span class="input-group-text">units</span>
                            </div>
                        </div>

                        <!-- Damage Reason -->
                        <div class="col-md-12">
                            <label for="damage_reason" class="form-label">Damage Reason/Description</label>
                            <textarea class="form-control" id="damage_reason" name="damage_reason" rows="3"
                                placeholder="Describe how the damage occurred..." required></textarea>
                        </div>

                        <!-- Loss Value Preview -->
                        <div class="col-md-12">
                            <div class="alert alert-danger mb-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-1">Estimated Loss Value</h6>
                                        <p class="mb-0" id="lossValueText">EGP 0.00</p>
                                    </div>
                                    <div class="fs-1 opacity-50">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Mark as Damaged
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('material_id').addEventListener('change', function() {
        const materialId = this.value;
        if (materialId) {
            // Fetch material details and update current stock
            fetch(`/api/materials/${materialId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('current_stock').value = data.stock_quantity;
                    updateLossValue(data.normal_price);
                });
        } else {
            document.getElementById('current_stock').value = '';
            document.getElementById('lossValueText').textContent = 'EGP 0.00';
        }
    });

    document.getElementById('damage_quantity').addEventListener('input', function() {
        const materialId = document.getElementById('material_id').value;
        if (materialId) {
            // Fetch material price and update loss value
            fetch(`/api/materials/${materialId}`)
                .then(response => response.json())
                .then(data => {
                    updateLossValue(data.normal_price);
                });
        }
    });

    function updateLossValue(unitPrice) {
        const quantity = document.getElementById('damage_quantity').value || 0;
        const lossValue = quantity * unitPrice;
        document.getElementById('lossValueText').textContent = `EGP ${lossValue.toFixed(2)}`;
    }

    // Form submission
    document.getElementById('markDamagedForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const materialId = document.getElementById('material_id').value;
        const quantity = document.getElementById('damage_quantity').value;
        const currentStock = document.getElementById('current_stock').value;

        if (parseInt(quantity) > parseInt(currentStock)) {
            alert('Damaged quantity cannot exceed current stock!');
            return;
        }

        // Submit form
        this.submit();
    });
</script>
