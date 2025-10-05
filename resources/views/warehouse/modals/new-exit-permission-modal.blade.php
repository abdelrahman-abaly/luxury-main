<!-- New Exit Permission Modal -->
<div class="modal fade" id="newExitPermissionModal" tabindex="-1" aria-labelledby="newExitPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="newExitPermissionModalLabel">
                    <i class="fas fa-door-open me-2"></i>
                    New Exit Permission
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="newExitPermissionForm" action="{{ route('warehouse.create-exit-permission') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Order Selection -->
                        <div class="col-md-12">
                            <label for="order_id" class="form-label">Select Order (Assigned to Driver)</label>
                            <select class="form-select" id="order_id" name="order_id" required>
                                <option value="">Choose order assigned to driver...</option>
                                @forelse($readyOrders ?? [] as $order)
                                <option value="{{ $order->id }}"
                                    data-customer="{{ $order->customer->name ?? '' }}"
                                    data-phone="{{ $order->customer->phone ?? '' }}"
                                    data-total="{{ $order->total }}"
                                    data-driver="{{ $order->deliveryAgent->name ?? 'Unknown Driver' }}"
                                    data-driver-id="{{ $order->delivery_agent_id }}">
                                    {{ $order->order_number }} - {{ $order->customer->name ?? 'Unknown Customer' }} (Driver: {{ $order->deliveryAgent->name ?? 'Unknown' }})
                                </option>
                                @empty
                                <option value="" disabled>No orders available - All products are out of stock</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Customer Details -->
                        <div class="col-md-4">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Customer Phone</label>
                            <input type="text" class="form-control" id="customer_phone" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Assigned Driver</label>
                            <input type="text" class="form-control" id="assigned_driver" readonly>
                        </div>

                        <!-- Expected Delivery Date -->
                        <div class="col-md-6">
                            <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                            <input type="date" class="form-control" id="expected_delivery_date"
                                name="expected_delivery_date" required
                                min="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Delivery Agent (Read-only) -->
                        <div class="col-md-6">
                            <label class="form-label">Delivery Agent</label>
                            <input type="text" class="form-control" id="delivery_agent_display" readonly>
                            <input type="hidden" id="delivery_agent_id" name="delivery_agent_id">
                        </div>

                        <!-- Products Preview -->
                        <div class="col-12">
                            <label class="form-label">Order Products</label>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsPreview">
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                Select an order to view products
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                placeholder="Add any special instructions or notes..."></textarea>
                        </div>

                        <!-- Value Preview -->
                        <div class="col-12">
                            <div class="alert alert-primary mb-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-1">Order Value</h6>
                                        <p class="mb-0" id="orderValueText">EGP 0.00</p>
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-door-open me-1"></i>
                        Create Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Update order details when order is selected
    document.getElementById('order_id').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];

        // Update customer details
        document.getElementById('customer_name').value = option.dataset.customer || '';
        document.getElementById('customer_phone').value = option.dataset.phone || '';
        document.getElementById('assigned_driver').value = option.dataset.driver || '';
        document.getElementById('delivery_agent_display').value = option.dataset.driver || '';
        document.getElementById('delivery_agent_id').value = option.dataset.driverId || '';

        // Debug logging
        console.log('Order selected:', {
            order_id: this.value,
            customer: option.dataset.customer,
            driver: option.dataset.driver,
            driver_id: option.dataset.driverId
        });

        // Validate driver_id
        if (!option.dataset.driverId || option.dataset.driverId.trim() === '') {
            console.error('No driver_id found in selected option');
            alert('Error: No driver assigned to this order. Please select a different order.');
            this.value = '';
            return;
        }

        // Update order value
        const total = parseFloat(option.dataset.total || 0);
        document.getElementById('orderValueText').textContent = `EGP ${total.toFixed(2)}`;

        // Update products preview
        if (this.value) {
            // First test with simple endpoint
            fetch(`/warehouse/api/test-order-products/${this.value}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Test API response:', data);
                })
                .catch(error => {
                    console.error('Test API error:', error);
                });

            // Then try the main endpoint
            fetch(`/warehouse/api/orders/${this.value}/products`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const tbody = document.getElementById('productsPreview');
                    tbody.innerHTML = '';

                    if (data && data.length > 0) {
                        data.forEach(product => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                            <td>${product.name}</td>
                            <td>${product.sku}</td>
                            <td>${product.quantity}</td>
                            <td>
                                <span class="badge bg-${product.stock_quantity >= product.quantity ? 'success' : 'danger'}">
                                    ${product.stock_quantity >= product.quantity ? 'In Stock' : 'Out of Stock'}
                                </span>
                            </td>
                        `;
                            tbody.appendChild(row);
                        });
                    } else {
                        tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No products found for this order
                            </td>
                        </tr>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                    const tbody = document.getElementById('productsPreview');
                    tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-danger">
                            Error loading products: ${error.message || 'Unknown error'}
                        </td>
                    </tr>
                `;
                });
        } else {
            document.getElementById('productsPreview').innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted">
                    Select an order to view products
                </td>
            </tr>
        `;
        }
    });

    // Set minimum date for expected delivery
    document.getElementById('expected_delivery_date').min = new Date().toISOString().split('T')[0];

    // Form validation
    document.getElementById('newExitPermissionForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const orderId = document.getElementById('order_id').value;
        if (!orderId) {
            alert('Please select an order.');
            return;
        }

        const agentId = document.getElementById('delivery_agent_id').value;
        if (!agentId) {
            alert('No delivery agent assigned to this order.');
            return;
        }

        // Debug logging
        console.log('Form submission data:', {
            order_id: orderId,
            delivery_agent_id: agentId,
            expected_delivery_date: document.getElementById('expected_delivery_date').value,
            notes: document.getElementById('notes').value
        });

        // Additional validation
        if (!orderId || orderId.trim() === '') {
            alert('Error: No order selected. Please select an order.');
            return;
        }

        if (!agentId || agentId.trim() === '') {
            alert('Error: No delivery agent ID found. Please refresh the page and try again.');
            return;
        }

        // Check if all products are in stock
        const outOfStock = document.querySelectorAll('#productsPreview .badge-danger');
        if (outOfStock.length > 0) {
            if (!confirm('Some products are out of stock. Do you want to continue anyway?')) {
                return;
            }
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
        submitBtn.disabled = true;

        // Ensure all required fields are filled
        const formData = new FormData(this);
        formData.set('delivery_agent_id', agentId);

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.set('_token', csrfToken);

        // Log form data before sending
        console.log('Sending form data:', Object.fromEntries(formData));

        // Submit form with fetch to handle errors better
        fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                if (response.ok) {
                    return response.json();
                }

                // Try to get error message from response
                return response.text().then(text => {
                    console.error('Error response text:', text);
                    throw new Error('Network response was not ok: ' + response.status + ' - ' + text);
                });
            })
            .then(data => {
                console.log('Response data:', data);

                if (data.success) {
                    alert(data.message);
                    // Close modal and reload page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newExitPermissionModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                console.error('Error stack:', error.stack);
                alert('Error creating exit permission: ' + error.message);
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
    });
</script>
