<!-- New Feeding Request Modal -->
<div class="modal fade" id="newFeedingRequestModal" tabindex="-1" aria-labelledby="newFeedingRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="newFeedingRequestModalLabel">
                    <i class="fas fa-utensils me-2"></i>
                    New Feeding Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="newFeedingRequestForm" action="{{ route('warehouse.create-feeding-request') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Order Details -->
                        <div class="col-md-6">
                            <label for="order_number" class="form-label">Order Number</label>
                            <input type="text" class="form-control" id="order_number" name="order_number"
                                placeholder="Enter order number" required>
                        </div>

                        <div class="col-md-6">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="">Select priority...</option>
                                <option value="urgent">Urgent</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>

                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name"
                                placeholder="Enter customer name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="customer_phone" class="form-label">Customer Phone</label>
                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone"
                                placeholder="Enter phone number" required>
                        </div>

                        <!-- Products Section -->
                        <div class="col-12">
                            <label class="form-label">Products</label>
                            <div id="productsContainer">
                                <div class="product-row mb-2">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <select class="form-select product-select" name="products[]" required>
                                                <option value="">Select product...</option>
                                                @foreach($products ?? [] as $product)
                                                <option value="{{ $product->id }}"
                                                    data-price="{{ $product->normal_price }}">
                                                    {{ $product->name }} ({{ $product->sku }})
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="number" class="form-control product-quantity"
                                                    name="quantities[]" min="1" placeholder="Qty" required>
                                                <span class="input-group-text">units</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-product">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-success mt-2" id="addProductBtn">
                                <i class="fas fa-plus me-1"></i>
                                Add Product
                            </button>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                placeholder="Add any special instructions or notes..."></textarea>
                        </div>

                        <!-- Total Preview -->
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-1">Total Value</h6>
                                        <p class="mb-0" id="totalValueText">EGP 0.00</p>
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
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-utensils me-1"></i>
                        Create Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Add product row
    document.getElementById('addProductBtn').addEventListener('click', function() {
        const container = document.getElementById('productsContainer');
        const newRow = container.querySelector('.product-row').cloneNode(true);

        // Clear values
        newRow.querySelector('.product-select').value = '';
        newRow.querySelector('.product-quantity').value = '';

        // Add event listeners
        addProductRowEventListeners(newRow);

        container.appendChild(newRow);
        updateTotal();
    });

    // Remove product row
    function addProductRowEventListeners(row) {
        row.querySelector('.remove-product').addEventListener('click', function() {
            if (document.querySelectorAll('.product-row').length > 1) {
                row.remove();
                updateTotal();
            }
        });

        row.querySelector('.product-select').addEventListener('change', updateTotal);
        row.querySelector('.product-quantity').addEventListener('input', updateTotal);
    }

    // Initialize event listeners for first row
    document.querySelectorAll('.product-row').forEach(row => {
        addProductRowEventListeners(row);
    });

    // Update total value
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const select = row.querySelector('.product-select');
            const quantity = row.querySelector('.product-quantity');

            if (select.value && quantity.value) {
                const price = parseFloat(select.options[select.selectedIndex].dataset.price);
                total += price * parseInt(quantity.value);
            }
        });

        document.getElementById('totalValueText').textContent = `EGP ${total.toFixed(2)}`;
    }

    // Form validation
    document.getElementById('newFeedingRequestForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate products
        let hasProducts = false;
        document.querySelectorAll('.product-row').forEach(row => {
            const select = row.querySelector('.product-select');
            const quantity = row.querySelector('.product-quantity');

            if (select.value && quantity.value) {
                hasProducts = true;
            }
        });

        if (!hasProducts) {
            alert('Please add at least one product to the request.');
            return;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creating...';
        submitBtn.disabled = true;

        // Submit form
        this.submit();
    });

    // Auto-fill order details
    document.getElementById('order_number').addEventListener('blur', function() {
        const orderNumber = this.value;
        if (orderNumber) {
            // Show loading
            const orderInput = this;
            const originalValue = orderInput.value;
            orderInput.value = 'Loading...';
            orderInput.disabled = true;

            // Fetch order details
            fetch(`/warehouse/api/orders/${orderNumber}`)
                .then(response => response.json())
                .then(data => {
                    orderInput.value = originalValue;
                    orderInput.disabled = false;

                    if (data.success && data.order.customer) {
                        document.getElementById('customer_name').value = data.order.customer.name;
                        document.getElementById('customer_phone').value = data.order.customer.phone;

                        // Auto-fill products if available
                        if (data.order.products && data.order.products.length > 0) {
                            const container = document.getElementById('productsContainer');
                            container.innerHTML = '';

                            data.order.products.forEach(product => {
                                const productRow = createProductRow();
                                const select = productRow.querySelector('.product-select');
                                const quantity = productRow.querySelector('.product-quantity');

                                select.value = product.id;
                                quantity.value = product.quantity;

                                container.appendChild(productRow);
                                addProductRowEventListeners(productRow);
                            });

                            updateTotal();
                        }
                    } else if (!data.success) {
                        alert('Order not found or error: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    orderInput.value = originalValue;
                    orderInput.disabled = false;
                    console.error('Error:', error);
                    alert('Failed to fetch order details');
                });
        }
    });

    // Helper function to create product row
    function createProductRow() {
        const template = document.querySelector('.product-row').cloneNode(true);
        template.querySelector('.product-select').value = '';
        template.querySelector('.product-quantity').value = '';
        return template;
    }
</script>
