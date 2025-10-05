@extends('layouts.main')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="dashboard-header mb-1">
                        <i class="fas fa-shopping-bag text-primary me-2"></i>
                        Shopping Bags Management
                    </h2>
                    <p class="text-muted mb-0">Manage shopping bags inventory and stock levels</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="toggleView()">
                        <i class="fas fa-th-large me-1"></i>
                        <span id="viewToggleText">Grid View</span>
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBagModal">
                        <i class="fas fa-plus me-1"></i>
                        Add Bag
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['total_bags'] ?? 0 }}</h4>
                            <small>Total Bags</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['in_stock'] ?? 0 }}</h4>
                            <small>In Stock</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-check-circle"></i>
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
                            <h4 class="mb-0">{{ $stats['out_of_stock'] ?? 0 }}</h4>
                            <small>Out of Stock</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-exclamation-triangle"></i>
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
                            <h4 class="mb-0">EGP {{ number_format($stats['total_value'] ?? 0, 2) }}</h4>
                            <small>Total Value</small>
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
            <form method="GET" action="{{ route('warehouse.shopping-bags') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                        value="{{ request('search') }}" placeholder="Search by name, SKU, or description">
                </div>
                <div class="col-md-3">
                    <label for="size" class="form-label">Size</label>
                    <select class="form-select" id="size" name="size">
                        <option value="">All Sizes</option>
                        <option value="small" {{ request('size') == 'small' ? 'selected' : '' }}>Small</option>
                        <option value="medium" {{ request('size') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="large" {{ request('size') == 'large' ? 'selected' : '' }}>Large</option>
                        <option value="xlarge" {{ request('size') == 'xlarge' ? 'selected' : '' }}>X-Large</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
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
                <h5 class="mb-0">Shopping Bags Inventory</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                        <i class="fas fa-check-square me-1"></i>
                        Select All
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="bulkUpdateStock()">
                        <i class="fas fa-edit me-1"></i>
                        Bulk Update
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
                            <th>Name</th>
                            <th>Size</th>
                            <th>Status</th>
                            <th>Stock Qty</th>
                            <th>Price</th>
                            <th>Total Value</th>
                            <th>Last Updated</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bags as $bag)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input bag-checkbox" value="{{ $bag->id }}">
                            </td>
                            <td>
                                @if($bag->image)
                                <img src="{{ $bag->image }}" alt="{{ $bag->name }}"
                                    class="rounded" width="40" height="40" style="object-fit: cover;">
                                @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-shopping-bag text-muted"></i>
                                </div>
                                @endif
                            </td>
                            <td><strong>{{ $bag->sku }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $bag->name }}</strong>
                                    @if($bag->description)
                                    <br><small class="text-muted">{{ Str::limit($bag->description, 50) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                $size = 'Unknown';
                                if (stripos($bag->name, 'small') !== false) $size = 'Small';
                                elseif (stripos($bag->name, 'medium') !== false) $size = 'Medium';
                                elseif (stripos($bag->name, 'large') !== false) $size = 'Large';
                                elseif (stripos($bag->name, 'xlarge') !== false) $size = 'X-Large';
                                @endphp
                                <span class="badge bg-info">{{ $size }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $bag->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($bag->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $bag->stock_quantity > 50 ? 'success' : ($bag->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                    {{ $bag->stock_quantity }}
                                </span>
                            </td>
                            <td>EGP {{ number_format($bag->normal_price, 2) }}</td>
                            <td>EGP {{ number_format($bag->stock_quantity * $bag->normal_price, 2) }}</td>
                            <td>{{ $bag->updated_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="editBag({{ $bag->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success"
                                        onclick="updateStock({{ $bag->id }}, {{ $bag->stock_quantity }})" title="Update Stock">
                                        <i class="fas fa-warehouse"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info"
                                        onclick="viewBag({{ $bag->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No shopping bags found</h5>
                                <p class="text-muted">Start by adding your first shopping bag to the inventory.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Grid View -->
    <div class="card d-none" id="gridView">
        <div class="card-header">
            <h5 class="mb-0">Grid View - Shopping Bags</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($bags as $bag)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 border-{{ $bag->stock_quantity > 50 ? 'success' : ($bag->stock_quantity > 0 ? 'warning' : 'danger') }}">
                        <div class="position-relative">
                            @if($bag->image)
                            <img src="{{ $bag->image }}" class="card-img-top" alt="{{ $bag->name }}"
                                style="height: 200px; object-fit: cover;">
                            @else
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                style="height: 200px;">
                                <i class="fas fa-shopping-bag fa-3x text-muted"></i>
                            </div>
                            @endif
                            <div class="position-absolute top-0 end-0 p-2">
                                <input type="checkbox" class="form-check-input bag-checkbox" value="{{ $bag->id }}">
                            </div>
                            <div class="position-absolute top-0 start-0 p-2">
                                <span class="badge bg-{{ $bag->stock_quantity > 50 ? 'success' : ($bag->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                    {{ $bag->stock_quantity }} in stock
                                </span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ $bag->name }}</h6>
                            <p class="card-text text-muted small">{{ $bag->sku }}</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-success fw-bold">EGP {{ number_format($bag->normal_price, 2) }}</span>
                                    <span class="badge bg-{{ $bag->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($bag->status) }}
                                    </span>
                                </div>
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="editBag({{ $bag->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="updateStock({{ $bag->id }}, {{ $bag->stock_quantity }})">
                                        <i class="fas fa-warehouse"></i>
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="viewBag({{ $bag->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No shopping bags found</h5>
                    <p class="text-muted">Start by adding your first shopping bag to the inventory.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <span class="text-muted">Showing {{ $bags->firstItem() ?? 0 }}-{{ $bags->lastItem() ?? 0 }} of {{ $bags->total() }} bags</span>
        </div>
        <nav aria-label="Page navigation">
            {{ $bags->links() }}
        </nav>
    </div>
</div>

<!-- Modals -->
@include('warehouse.modals.add-bag-modal')
@include('warehouse.modals.edit-bag-modal')
@include('warehouse.modals.update-stock-modal')
@include('warehouse.modals.bulk-update-modal')
@include('warehouse.modals.view-bag-modal')

<script>
    function toggleView() {
        const tableView = document.getElementById('tableView');
        const gridView = document.getElementById('gridView');
        const toggleText = document.getElementById('viewToggleText');

        if (tableView.classList.contains('d-none')) {
            tableView.classList.remove('d-none');
            gridView.classList.add('d-none');
            toggleText.textContent = 'Grid View';
        } else {
            tableView.classList.add('d-none');
            gridView.classList.remove('d-none');
            toggleText.textContent = 'Table View';
        }
    }

    function selectAll() {
        const checkboxes = document.querySelectorAll('.bag-checkbox');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    function editBag(bagId) {
        if (!bagId) {
            alert('Invalid bag ID');
            return;
        }

        // Fetch bag data and populate edit modal
        fetch(`/warehouse/shopping-bags/${bagId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate edit modal with bag data
                    document.getElementById('editBagName').value = data.bag.name;
                    document.getElementById('editBagSku').value = data.bag.sku;
                    document.getElementById('editBagDescription').value = data.bag.description || '';
                    document.getElementById('editBagPrice').value = data.bag.normal_price;
                    document.getElementById('editBagSalePrice').value = data.bag.sale_price;
                    document.getElementById('editBagStock').value = data.bag.stock_quantity;
                    document.getElementById('editBagStatus').value = data.bag.status;
                    document.getElementById('editBagSize').value = data.bag.size || '';
                    document.getElementById('editBagColor').value = data.bag.color || '';
                    document.getElementById('editBagImage').value = data.bag.images || '';

                    // Set the form action
                    document.getElementById('editBagForm').action = `/warehouse/shopping-bags/${bagId}`;

                    // Show edit modal
                    new bootstrap.Modal(document.getElementById('editBagModal')).show();
                } else {
                    alert('Failed to load bag data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading bag data');
            });
    }

    function updateStock(bagId, currentStock) {
        if (!bagId) {
            alert('Invalid bag ID');
            return;
        }

        const newStock = prompt(`Enter new stock quantity for this bag:\nCurrent stock: ${currentStock}`, currentStock);

        if (newStock === null) return; // User cancelled

        const stockQuantity = parseInt(newStock);
        if (isNaN(stockQuantity) || stockQuantity < 0) {
            alert('Please enter a valid stock quantity');
            return;
        }

        const notes = prompt('Enter notes for this stock update (optional):', '');

        // Update stock via API
        fetch(`/warehouse/shopping-bags/${bagId}/stock`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    stock_quantity: stockQuantity,
                    notes: notes || ''
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Stock updated successfully!');
                    location.reload(); // Refresh the page to show updated data
                } else {
                    alert('Failed to update stock: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating stock');
            });
    }

    function viewBag(bagId) {
        if (!bagId) {
            alert('Invalid bag ID');
            return;
        }

        // Fetch bag data and populate view modal
        fetch(`/warehouse/shopping-bags/${bagId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate view modal with bag data
                    document.getElementById('viewBagName').textContent = data.bag.name;
                    document.getElementById('viewBagSku').textContent = data.bag.sku;
                    document.getElementById('viewBagDescription').textContent = data.bag.description || 'No description';
                    document.getElementById('viewBagPrice').textContent = 'EGP ' + parseFloat(data.bag.normal_price).toFixed(2);
                    document.getElementById('viewBagSalePrice').textContent = 'EGP ' + parseFloat(data.bag.sale_price).toFixed(2);
                    document.getElementById('viewBagStock').textContent = data.bag.stock_quantity;
                    document.getElementById('viewBagStatus').textContent = data.bag.status.charAt(0).toUpperCase() + data.bag.status.slice(1);
                    document.getElementById('viewBagSize').textContent = data.bag.size || 'Not specified';
                    document.getElementById('viewBagColor').textContent = data.bag.color || 'Not specified';
                    document.getElementById('viewBagCategory').textContent = data.bag.category;

                    // Show view modal
                    new bootstrap.Modal(document.getElementById('viewBagModal')).show();
                } else {
                    alert('Failed to load bag data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading bag data');
            });
    }

    function bulkUpdateStock() {
        const selectedBags = document.querySelectorAll('.bag-checkbox:checked');
        if (selectedBags.length === 0) {
            alert('Please select bags to update');
            return;
        }

        const newStock = prompt(`Enter new stock quantity for ${selectedBags.length} selected bags:`, '0');
        if (newStock === null) return; // User cancelled

        const stockQuantity = parseInt(newStock);
        if (isNaN(stockQuantity) || stockQuantity < 0) {
            alert('Please enter a valid stock quantity');
            return;
        }

        const notes = prompt('Enter notes for this bulk stock update (optional):', '');

        // Update stock for all selected bags
        const promises = Array.from(selectedBags).map(checkbox => {
            const bagId = checkbox.value;
            return fetch(`/warehouse/shopping-bags/${bagId}/stock`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    stock_quantity: stockQuantity,
                    notes: notes || ''
                })
            });
        });

        Promise.all(promises)
            .then(responses => Promise.all(responses.map(r => r.json())))
            .then(results => {
                const successCount = results.filter(r => r.success).length;
                const failCount = results.length - successCount;

                if (failCount === 0) {
                    alert(`Successfully updated stock for ${successCount} bags!`);
                } else {
                    alert(`Updated ${successCount} bags successfully, ${failCount} failed.`);
                }

                location.reload(); // Refresh the page
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating stock');
            });
    }

    // Select all checkbox functionality
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.bag-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Handle add bag form submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('addBagForm');
        if (!form) {
            console.error('Add bag form not found!');
            return;
        }

        console.log('Add bag form found, adding event listener...');

        form.addEventListener('submit', function(e) {
            console.log('Form submit event triggered!');
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Adding...';
            submitBtn.disabled = true;

            // Log form data for debugging
            console.log('Form action:', this.action);
            for (let [key, value] of formData.entries()) {
                console.log('Form data:', key, '=', value);
            }

            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json().then(data => {
                        console.log('Response data:', data);
                        if (!response.ok) {
                            // Create a custom error with the response data
                            const error = new Error(`HTTP error! status: ${response.status}`);
                            error.response = data;
                            error.status = response.status;
                            throw error;
                        }
                        return data;
                    }).catch(jsonError => {
                        console.error('JSON parse error:', jsonError);
                        throw new Error('Invalid response from server');
                    });
                })
                .then(data => {
                    if (data.success) {
                        console.log('Success! Reloading page...');

                        // Close modal before alert
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addBagModal'));
                        if (modal) {
                            modal.hide();
                        }

                        // Show success message
                        alert('Bag added successfully!');

                        // Force redirect to same page with cache busting
                        const url = window.location.href.split('#')[0].split('?')[0];
                        window.location.href = url + '?t=' + new Date().getTime();
                    } else {
                        let errorMessage = 'Failed to add bag: ' + data.message;
                        if (data.errors) {
                            errorMessage += '\nValidation errors:\n';
                            for (const field in data.errors) {
                                errorMessage += field + ': ' + data.errors[field].join(', ') + '\n';
                            }
                        }
                        alert(errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMessage = 'An error occurred while adding the bag';

                    // Check if it's a validation error
                    if (error.status === 422 && error.response) {
                        if (error.response.message) {
                            errorMessage = error.response.message;
                        } else if (error.response.errors) {
                            errorMessage = 'Validation errors:\n';
                            for (const field in error.response.errors) {
                                errorMessage += field + ': ' + error.response.errors[field].join(', ') + '\n';
                            }
                        }
                    } else if (error.message.includes('422')) {
                        errorMessage = 'Validation error: The SKU might already exist. Please try a different SKU.';
                    } else if (error.message.includes('Invalid response')) {
                        errorMessage = 'Server returned invalid response. Please try again.';
                    }

                    alert(errorMessage);
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    });

    // Handle edit bag form submission
    document.getElementById('editBagForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
        submitBtn.disabled = true;

        fetch(this.action, {
                method: 'PUT',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Bag updated successfully!');
                    location.reload();
                } else {
                    alert('Failed to update bag: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the bag');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
    });
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
