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
                        <i class="fas fa-boxes text-primary me-2"></i>
                        Boxes Management
                    </h2>
                    <p class="text-muted mb-0">Manage packaging boxes inventory and stock levels</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="toggleView()">
                        <i class="fas fa-th-large me-1"></i>
                        <span id="viewToggleText">Grid View</span>
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoxModal">
                        <i class="fas fa-plus me-1"></i>
                        Add Box
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
                            <h4 class="mb-0">{{ $stats['total_boxes'] ?? 0 }}</h4>
                            <small>Total Boxes</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-boxes"></i>
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
            <form method="GET" action="{{ route('warehouse.boxes') }}" class="row g-3">
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
                <h5 class="mb-0">Boxes Inventory</h5>
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
                        @forelse($boxes as $box)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input box-checkbox" value="{{ $box->id }}">
                            </td>
                            <td>
                                @if($box->image)
                                <img src="{{ $box->image }}" alt="{{ $box->name }}"
                                    class="rounded" width="40" height="40" style="object-fit: cover;">
                                @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-box text-muted"></i>
                                </div>
                                @endif
                            </td>
                            <td><strong>{{ $box->sku }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $box->name }}</strong>
                                    @if($box->description)
                                    <br><small class="text-muted">{{ Str::limit($box->description, 50) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                $size = 'Unknown';
                                if (stripos($box->name, 'small') !== false) $size = 'Small';
                                elseif (stripos($box->name, 'medium') !== false) $size = 'Medium';
                                elseif (stripos($box->name, 'large') !== false) $size = 'Large';
                                elseif (stripos($box->name, 'xlarge') !== false) $size = 'X-Large';
                                @endphp
                                <span class="badge bg-info">{{ $size }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $box->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($box->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $box->stock_quantity > 10 ? 'success' : ($box->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                    {{ $box->stock_quantity }}
                                </span>
                            </td>
                            <td>EGP {{ number_format($box->normal_price, 2) }}</td>
                            <td>EGP {{ number_format($box->stock_quantity * $box->normal_price, 2) }}</td>
                            <td>{{ $box->updated_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="editBox({{ $box->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success"
                                        onclick="updateStock({{ $box->id }}, {{ $box->stock_quantity }})" title="Update Stock">
                                        <i class="fas fa-warehouse"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info"
                                        onclick="viewBox({{ $box->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No boxes found</h5>
                                <p class="text-muted">Start by adding your first box to the inventory.</p>
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
            <h5 class="mb-0">Grid View - Boxes</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($boxes as $box)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 border-{{ $box->stock_quantity > 10 ? 'success' : ($box->stock_quantity > 0 ? 'warning' : 'danger') }}">
                        <div class="position-relative">
                            @if($box->image)
                            <img src="{{ $box->image }}" class="card-img-top" alt="{{ $box->name }}"
                                style="height: 200px; object-fit: cover;">
                            @else
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                style="height: 200px;">
                                <i class="fas fa-box fa-3x text-muted"></i>
                            </div>
                            @endif
                            <div class="position-absolute top-0 end-0 p-2">
                                <input type="checkbox" class="form-check-input box-checkbox" value="{{ $box->id }}">
                            </div>
                            <div class="position-absolute top-0 start-0 p-2">
                                <span class="badge bg-{{ $box->stock_quantity > 10 ? 'success' : ($box->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                    {{ $box->stock_quantity }} in stock
                                </span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ $box->name }}</h6>
                            <p class="card-text text-muted small">{{ $box->sku }}</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-success fw-bold">EGP {{ number_format($box->normal_price, 2) }}</span>
                                    <span class="badge bg-{{ $box->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($box->status) }}
                                    </span>
                                </div>
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="editBox({{ $box->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="updateStock({{ $box->id }}, {{ $box->stock_quantity }})">
                                        <i class="fas fa-warehouse"></i>
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="viewBox({{ $box->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No boxes found</h5>
                    <p class="text-muted">Start by adding your first box to the inventory.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <span class="text-muted">Showing {{ $boxes->firstItem() ?? 0 }}-{{ $boxes->lastItem() ?? 0 }} of {{ $boxes->total() }} boxes</span>
        </div>
        <nav aria-label="Page navigation">
            {{ $boxes->links() }}
        </nav>
    </div>
</div>

<!-- Modals -->
@include('warehouse.modals.add-box-modal')
@include('warehouse.modals.edit-box-modal')
@include('warehouse.modals.update-stock-modal')
@include('warehouse.modals.bulk-update-modal')
@include('warehouse.modals.view-box-modal')

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
        const checkboxes = document.querySelectorAll('.box-checkbox');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    function editBox(boxId) {
        if (!boxId) {
            alert('Invalid box ID');
            return;
        }

        // Fetch box data and populate edit modal
        fetch(`/warehouse/boxes/${boxId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate edit modal with box data
                    document.getElementById('editBoxName').value = data.box.name;
                    document.getElementById('editBoxSku').value = data.box.sku;
                    document.getElementById('editBoxDescription').value = data.box.description || '';
                    document.getElementById('editBoxNormalPrice').value = data.box.normal_price;
                    document.getElementById('editBoxSalePrice').value = data.box.sale_price;
                    document.getElementById('editBoxStock').value = data.box.stock_quantity;
                    document.getElementById('editBoxStatus').value = data.box.status;
                    document.getElementById('editBoxSize').value = data.box.size || '';
                    document.getElementById('editBoxImage').value = data.box.images || '';

                    // Set the form action
                    document.getElementById('editBoxForm').action = `/warehouse/boxes/${boxId}`;

                    // Show edit modal
                    new bootstrap.Modal(document.getElementById('editBoxModal')).show();
                } else {
                    alert('Failed to load box data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading box data');
            });
    }

    function updateStock(boxId, currentStock) {
        if (!boxId) {
            alert('Invalid box ID');
            return;
        }

        const newStock = prompt(`Enter new stock quantity for this box:\nCurrent stock: ${currentStock}`, currentStock);

        if (newStock === null) return; // User cancelled

        const stockQuantity = parseInt(newStock);
        if (isNaN(stockQuantity) || stockQuantity < 0) {
            alert('Please enter a valid stock quantity');
            return;
        }

        const notes = prompt('Enter notes for this stock update (optional):', '');

        // Update stock via API
        fetch(`/warehouse/boxes/${boxId}/stock`, {
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

    function viewBox(boxId) {
        if (!boxId) {
            alert('Invalid box ID');
            return;
        }

        // Fetch box data and populate view modal
        fetch(`/warehouse/boxes/${boxId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate view modal with box data
                    document.getElementById('viewBoxName').textContent = data.box.name;
                    document.getElementById('viewBoxSku').textContent = data.box.sku;
                    document.getElementById('viewBoxDescription').textContent = data.box.description || 'No description';
                    document.getElementById('viewBoxPrice').textContent = 'EGP ' + parseFloat(data.box.normal_price).toFixed(2);
                    document.getElementById('viewBoxSalePrice').textContent = 'EGP ' + parseFloat(data.box.sale_price).toFixed(2);
                    document.getElementById('viewBoxStock').textContent = data.box.stock_quantity;
                    document.getElementById('viewBoxStatus').textContent = data.box.status.charAt(0).toUpperCase() + data.box.status.slice(1);
                    document.getElementById('viewBoxSize').textContent = data.box.size || 'Not specified';
                    document.getElementById('viewBoxCategory').textContent = data.box.category;

                    // Show view modal
                    new bootstrap.Modal(document.getElementById('viewBoxModal')).show();
                } else {
                    alert('Failed to load box data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading box data');
            });
    }

    function bulkUpdateStock() {
        const selectedBoxes = document.querySelectorAll('.box-checkbox:checked');
        if (selectedBoxes.length === 0) {
            alert('Please select boxes to update');
            return;
        }

        const newStock = prompt(`Enter new stock quantity for ${selectedBoxes.length} selected boxes:`, '0');
        if (newStock === null) return; // User cancelled

        const stockQuantity = parseInt(newStock);
        if (isNaN(stockQuantity) || stockQuantity < 0) {
            alert('Please enter a valid stock quantity');
            return;
        }

        const notes = prompt('Enter notes for this bulk stock update (optional):', '');

        // Update stock for all selected boxes
        const promises = Array.from(selectedBoxes).map(checkbox => {
            const boxId = checkbox.value;
            return fetch(`/warehouse/boxes/${boxId}/stock`, {
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
                    alert(`Successfully updated stock for ${successCount} boxes!`);
                } else {
                    alert(`Updated ${successCount} boxes successfully, ${failCount} failed.`);
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
        const checkboxes = document.querySelectorAll('.box-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Handle add box form submission
    document.getElementById('addBoxForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Adding...';
        submitBtn.disabled = true;

        fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Box added successfully!');
                    location.reload();
                } else {
                    alert('Failed to add box: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the box');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
    });

    // Handle edit box form submission
    document.getElementById('editBoxForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading state
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
                    alert('Box updated successfully!');
                    location.reload();
                } else {
                    alert('Failed to update box: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the box');
            })
            .finally(() => {
                // Reset button state
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
