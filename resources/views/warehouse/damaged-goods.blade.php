@extends('layouts.main')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="dashboard-header mb-1">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Damaged Goods Management
                    </h2>
                    <p class="text-muted mb-0">Track and manage damaged products inventory</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="toggleView()">
                        <i class="fas fa-th-large me-1"></i>
                        <span id="viewToggleText">Grid View</span>
                    </button>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#markDamagedModal">
                        <i class="fas fa-plus me-1"></i>
                        Mark as Damaged
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['total_damaged'] ?? 0 }}</h4>
                            <small>Total Damaged</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['critical_damage'] ?? 0 }}</h4>
                            <small>Critical Damage</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['repairable'] ?? 0 }}</h4>
                            <small>Repairable</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-tools"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">EGP {{ number_format($stats['total_loss'] ?? 0, 2) }}</h4>
                            <small>Total Loss Value</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.damaged-goods') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                        value="{{ request('search') }}" placeholder="Search by name, SKU, or description">
                </div>
                <div class="col-md-2">
                    <label for="damage_level" class="form-label">Damage Level</label>
                    <select class="form-select" id="damage_level" name="damage_level">
                        <option value="">All Levels</option>
                        <option value="minor" {{ request('damage_level') == 'minor' ? 'selected' : '' }}>Minor</option>
                        <option value="moderate" {{ request('damage_level') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                        <option value="severe" {{ request('damage_level') == 'severe' ? 'selected' : '' }}>Severe</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from"
                        value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to"
                        value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table View -->
    <div class="card" id="tableView">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Damaged Products Inventory</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                        <i class="fas fa-check-square me-1"></i>
                        Select All
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="bulkAction()">
                        <i class="fas fa-trash me-1"></i>
                        Bulk Action
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                            </th>
                            <th>Image</th>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Damage Level</th>
                            <th>Damaged Qty</th>
                            <th>Loss Value</th>
                            <th>Date Damaged</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($damagedGoods as $damagedItem)
                        <tr class="table-warning">
                            <td>
                                <input type="checkbox" class="form-check-input damaged-checkbox" value="{{ $damagedItem->id }}">
                            </td>
                            <td>
                                @if($damagedItem->product->image)
                                <img src="{{ $damagedItem->product->image }}" alt="{{ $damagedItem->product->name }}"
                                    class="rounded" width="40" height="40" style="object-fit: cover;">
                                @else
                                <div class="bg-warning bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-box text-warning"></i>
                                </div>
                                @endif
                            </td>
                            <td><strong>{{ $damagedItem->product->sku }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $damagedItem->product->name }}</strong>
                                    @if($damagedItem->product->description)
                                    <br><small class="text-muted">{{ Str::limit(strip_tags($damagedItem->product->description), 50) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $damagedItem->damage_level == 'severe' ? 'danger' : ($damagedItem->damage_level == 'minor' ? 'info' : 'warning') }}">
                                    {{ ucfirst($damagedItem->damage_level) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger">
                                    {{ $damagedItem->damaged_quantity }}
                                </span>
                            </td>
                            <td>EGP {{ number_format($damagedItem->damaged_quantity * $damagedItem->product->normal_price, 2) }}</td>
                            <td>{{ $damagedItem->reported_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $damagedItem->status == 'reported' ? 'warning' : ($damagedItem->status == 'repaired' ? 'success' : 'danger') }}">
                                    {{ ucfirst($damagedItem->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-info"
                                        onclick="viewDamaged({{ $damagedItem->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($damagedItem->status == 'reported')
                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick="repairProduct({{ $damagedItem->id }})" title="Repair">
                                        <i class="fas fa-tools"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="disposeProduct({{ $damagedItem->id }})" title="Dispose">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No damaged goods found</h5>
                                <p class="text-muted">All products are in good condition.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <span class="text-muted">Showing {{ $damagedGoods->firstItem() ?? 0 }}-{{ $damagedGoods->lastItem() ?? 0 }} of {{ $damagedGoods->total() }} damaged products</span>
        </div>
        <nav aria-label="Page navigation">
            {{ $damagedGoods->links() }}
        </nav>
    </div>
</div>

<script>
    function toggleView() {
        const tableView = document.getElementById('tableView');
        const toggleText = document.getElementById('viewToggleText');

        // For now, just toggle text (grid view can be added later)
        if (toggleText.textContent === 'Grid View') {
            toggleText.textContent = 'Table View';
        } else {
            toggleText.textContent = 'Grid View';
        }
    }

    function selectAll() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const checkboxes = document.querySelectorAll('.damaged-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    function viewDamaged(id) {
        console.log('View damaged item:', id);
        // TODO: Implement view functionality
        alert('View functionality will be implemented');
    }

    function repairProduct(id) {
        if (confirm('Are you sure you want to mark this item as repaired?')) {
            updateDamagedItemStatus(id, 'repaired');
        }
    }

    function disposeProduct(id) {
        if (confirm('Are you sure you want to dispose this item? This action cannot be undone.')) {
            updateDamagedItemStatus(id, 'disposed');
        }
    }

    function updateDamagedItemStatus(id, status) {
        fetch(`/warehouse/damaged-items/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: status,
                    notes: 'Status updated via interface'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    location.reload();
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred while updating status', 'danger');
            });
    }

    function bulkAction() {
        const selectedProducts = document.querySelectorAll('.damaged-checkbox:checked');
        if (selectedProducts.length === 0) {
            alert('Please select products to perform bulk action');
            return;
        }
        console.log('Bulk action for products:', Array.from(selectedProducts).map(cb => cb.value));
    }

    // Select all checkbox functionality
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.damaged-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>

<!-- Mark as Damaged Modal -->
<div class="modal fade" id="markDamagedModal" tabindex="-1" aria-labelledby="markDamagedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markDamagedModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Mark Product as Damaged
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="markDamagedForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Select Product <span class="text-danger">*</span></label>
                                <select class="form-select" id="product_id" name="product_id" required>
                                    <option value="">Choose a product...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="damaged_quantity" class="form-label">Damaged Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="damaged_quantity" name="damaged_quantity" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="damage_level" class="form-label">Damage Level <span class="text-danger">*</span></label>
                                <select class="form-select" id="damage_level" name="damage_level" required>
                                    <option value="">Select damage level...</option>
                                    <option value="minor">Minor</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="severe">Severe</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="damage_reason" class="form-label">Damage Reason</label>
                                <input type="text" class="form-control" id="damage_reason" name="damage_reason" placeholder="e.g., Transport damage, Storage issue...">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes about the damage..."></textarea>
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
    // Load available products when modal opens
    document.getElementById('markDamagedModal').addEventListener('show.bs.modal', function() {
        loadAvailableProducts();
    });

    function loadAvailableProducts() {
        fetch('/warehouse/available-products?type=goods')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('product_id');
                select.innerHTML = '<option value="">Choose a product...</option>';

                if (data.success && data.data) {
                    data.data.forEach(product => {
                        const option = document.createElement('option');
                        option.value = product.id;
                        option.textContent = `${product.name} (Stock: ${product.stock_quantity})`;
                        option.dataset.stock = product.stock_quantity;
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading products:', error);
                showAlert('Error loading products', 'danger');
            });
    }

    // Update max quantity when product changes
    document.getElementById('product_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const maxQuantity = selectedOption.dataset.stock || 0;
        const quantityInput = document.getElementById('damaged_quantity');
        quantityInput.max = maxQuantity;
        quantityInput.placeholder = `Max: ${maxQuantity}`;
    });

    // Handle form submission
    document.getElementById('markDamagedForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/warehouse/mark-as-damaged', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('markDamagedModal')).hide();
                    location.reload();
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred while marking product as damaged', 'danger');
            });
    });

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.insertBefore(alertDiv, document.body.firstChild);

        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
</script>

<style>
    .dashboard-header {
        color: #2c3e50;
        font-weight: 600;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }

    .btn-group .btn {
        border-radius: 0.375rem;
    }

    .badge {
        font-size: 0.75em;
    }
</style>
@endsection