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
                        <i class="fas fa-file-alt text-info me-2"></i>
                        Flyerz Management
                    </h2>
                    <p class="text-muted mb-0">Manage flyers, brochures, and promotional materials inventory</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="toggleView()">
                        <i class="fas fa-th-large me-1"></i>
                        <span id="viewToggleText">Grid View</span>
                    </button>
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addFlyerModal">
                        <i class="fas fa-plus me-1"></i>
                        Add Flyer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['total_flyerz'] ?? 0 }}</h4>
                            <small>Total Flyerz</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-file-alt"></i>
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
            <div class="card bg-warning text-white">
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
            <form method="GET" action="{{ route('warehouse.flyerz') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                        value="{{ request('search') }}" placeholder="Search by name, SKU, or description">
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="flyer" {{ request('type') == 'flyer' ? 'selected' : '' }}>Flyer</option>
                        <option value="brochure" {{ request('type') == 'brochure' ? 'selected' : '' }}>Brochure</option>
                        <option value="leaflet" {{ request('type') == 'leaflet' ? 'selected' : '' }}>Leaflet</option>
                        <option value="catalog" {{ request('type') == 'catalog' ? 'selected' : '' }}>Catalog</option>
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
                <h5 class="mb-0">Flyerz Inventory</h5>
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
                            <th>Type</th>
                            <th>Status</th>
                            <th>Stock Qty</th>
                            <th>Price</th>
                            <th>Total Value</th>
                            <th>Last Updated</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flyerz as $flyer)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input flyer-checkbox" value="{{ $flyer->id }}">
                            </td>
                            <td>
                                @if($flyer->image)
                                <img src="{{ $flyer->image }}" alt="{{ $flyer->name }}"
                                    class="rounded" width="40" height="40" style="object-fit: cover;">
                                @else
                                <div class="bg-info bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-file-alt text-info"></i>
                                </div>
                                @endif
                            </td>
                            <td><strong>{{ $flyer->sku }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $flyer->name }}</strong>
                                    @if($flyer->description)
                                    <br><small class="text-muted">{{ Str::limit($flyer->description, 50) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                $type = 'Flyer';
                                if (stripos($flyer->name, 'brochure') !== false) $type = 'Brochure';
                                elseif (stripos($flyer->name, 'leaflet') !== false) $type = 'Leaflet';
                                elseif (stripos($flyer->name, 'catalog') !== false) $type = 'Catalog';
                                @endphp
                                <span class="badge bg-info">{{ $type }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $flyer->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($flyer->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $flyer->stock_quantity > 100 ? 'success' : ($flyer->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                    {{ $flyer->stock_quantity }}
                                </span>
                            </td>
                            <td>EGP {{ number_format($flyer->normal_price, 2) }}</td>
                            <td>EGP {{ number_format($flyer->stock_quantity * $flyer->normal_price, 2) }}</td>
                            <td>{{ $flyer->updated_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="editFlyer({{ $flyer->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success"
                                        onclick="updateStock({{ $flyer->id }}, {{ $flyer->stock_quantity }})" title="Update Stock">
                                        <i class="fas fa-warehouse"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info"
                                        onclick="viewFlyer({{ $flyer->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No flyerz found</h5>
                                <p class="text-muted">Start by adding your first flyer to the inventory.</p>
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
            <h5 class="mb-0">Grid View - Flyerz</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($flyerz as $flyer)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 border-{{ $flyer->stock_quantity > 100 ? 'success' : ($flyer->stock_quantity > 0 ? 'warning' : 'danger') }}">
                        <div class="position-relative">
                            @if($flyer->image)
                            <img src="{{ $flyer->image }}" class="card-img-top" alt="{{ $flyer->name }}"
                                style="height: 200px; object-fit: cover;">
                            @else
                            <div class="bg-info bg-opacity-10 d-flex align-items-center justify-content-center"
                                style="height: 200px;">
                                <i class="fas fa-file-alt fa-3x text-info"></i>
                            </div>
                            @endif
                            <div class="position-absolute top-0 end-0 p-2">
                                <input type="checkbox" class="form-check-input flyer-checkbox" value="{{ $flyer->id }}">
                            </div>
                            <div class="position-absolute top-0 start-0 p-2">
                                <span class="badge bg-{{ $flyer->stock_quantity > 100 ? 'success' : ($flyer->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                    {{ $flyer->stock_quantity }} in stock
                                </span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ $flyer->name }}</h6>
                            <p class="card-text text-muted small">{{ $flyer->sku }}</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-info fw-bold">EGP {{ number_format($flyer->normal_price, 2) }}</span>
                                    <span class="badge bg-{{ $flyer->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($flyer->status) }}
                                    </span>
                                </div>
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="editFlyer({{ $flyer->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="updateStock({{ $flyer->id }}, {{ $flyer->stock_quantity }})">
                                        <i class="fas fa-warehouse"></i>
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="viewFlyer({{ $flyer->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No flyerz found</h5>
                    <p class="text-muted">Start by adding your first flyer to the inventory.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <span class="text-muted">Showing {{ $flyerz->firstItem() ?? 0 }}-{{ $flyerz->lastItem() ?? 0 }} of {{ $flyerz->total() }} flyerz</span>
        </div>
        <nav aria-label="Page navigation">
            {{ $flyerz->links() }}
        </nav>
    </div>
</div>

<!-- Modals -->
@include('warehouse.modals.add-flyer-modal')
@include('warehouse.modals.edit-flyer-modal')
@include('warehouse.modals.update-stock-modal')
@include('warehouse.modals.bulk-update-modal')
@include('warehouse.modals.view-flyer-modal')

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
        const checkboxes = document.querySelectorAll('.flyer-checkbox');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    function editFlyer(flyerId) {
        if (!flyerId) {
            alert('Invalid flyer ID');
            return;
        }

        // Fetch flyer data and populate edit modal
        fetch(`/warehouse/flyerz/${flyerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate edit modal with flyer data
                    document.getElementById('editFlyerName').value = data.flyer.name;
                    document.getElementById('editFlyerSku').value = data.flyer.sku;
                    document.getElementById('editFlyerDescription').value = data.flyer.description || '';
                    document.getElementById('editFlyerPrice').value = data.flyer.normal_price;
                    document.getElementById('editFlyerSalePrice').value = data.flyer.sale_price;
                    document.getElementById('editFlyerStock').value = data.flyer.stock_quantity;
                    document.getElementById('editFlyerStatus').value = data.flyer.status;
                    document.getElementById('editFlyerSize').value = data.flyer.size || '';
                    document.getElementById('editFlyerColor').value = data.flyer.color || '';
                    document.getElementById('editFlyerImage').value = data.flyer.images || '';

                    // Set the form action
                    document.getElementById('editFlyerForm').action = `/warehouse/flyerz/${flyerId}`;

                    // Show edit modal
                    new bootstrap.Modal(document.getElementById('editFlyerModal')).show();
                } else {
                    alert('Failed to load flyer data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading flyer data');
            });
    }

    function updateStock(flyerId, currentStock) {
        if (!flyerId) {
            alert('Invalid flyer ID');
            return;
        }

        const newStock = prompt(`Enter new stock quantity for this flyer:\nCurrent stock: ${currentStock}`, currentStock);

        if (newStock === null) return; // User cancelled

        const stockQuantity = parseInt(newStock);
        if (isNaN(stockQuantity) || stockQuantity < 0) {
            alert('Please enter a valid stock quantity');
            return;
        }

        const notes = prompt('Enter notes for this stock update (optional):', '');

        // Update stock via API
        fetch(`/warehouse/flyerz/${flyerId}/stock`, {
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

    function viewFlyer(flyerId) {
        if (!flyerId) {
            alert('Invalid flyer ID');
            return;
        }

        // Fetch flyer data and populate view modal
        fetch(`/warehouse/flyerz/${flyerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate view modal with flyer data
                    document.getElementById('viewFlyerName').textContent = data.flyer.name;
                    document.getElementById('viewFlyerSku').textContent = data.flyer.sku;
                    document.getElementById('viewFlyerDescription').textContent = data.flyer.description || 'No description';
                    document.getElementById('viewFlyerPrice').textContent = 'EGP ' + parseFloat(data.flyer.normal_price).toFixed(2);
                    document.getElementById('viewFlyerSalePrice').textContent = 'EGP ' + parseFloat(data.flyer.sale_price).toFixed(2);
                    document.getElementById('viewFlyerStock').textContent = data.flyer.stock_quantity;
                    document.getElementById('viewFlyerStatus').textContent = data.flyer.status.charAt(0).toUpperCase() + data.flyer.status.slice(1);
                    document.getElementById('viewFlyerSize').textContent = data.flyer.size || 'Not specified';
                    document.getElementById('viewFlyerColor').textContent = data.flyer.color || 'Not specified';
                    document.getElementById('viewFlyerCategory').textContent = data.flyer.category;

                    // Show view modal
                    new bootstrap.Modal(document.getElementById('viewFlyerModal')).show();
                } else {
                    alert('Failed to load flyer data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading flyer data');
            });
    }

    function bulkUpdateStock() {
        const selectedFlyerz = document.querySelectorAll('.flyer-checkbox:checked');
        if (selectedFlyerz.length === 0) {
            alert('Please select flyerz to update');
            return;
        }

        const newStock = prompt(`Enter new stock quantity for ${selectedFlyerz.length} selected flyerz:`, '0');
        if (newStock === null) return; // User cancelled

        const stockQuantity = parseInt(newStock);
        if (isNaN(stockQuantity) || stockQuantity < 0) {
            alert('Please enter a valid stock quantity');
            return;
        }

        const notes = prompt('Enter notes for this bulk stock update (optional):', '');

        // Update stock for all selected flyerz
        const promises = Array.from(selectedFlyerz).map(checkbox => {
            const flyerId = checkbox.value;
            return fetch(`/warehouse/flyerz/${flyerId}/stock`, {
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
                    alert(`Successfully updated stock for ${successCount} flyerz!`);
                } else {
                    alert(`Updated ${successCount} flyerz successfully, ${failCount} failed.`);
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
        const checkboxes = document.querySelectorAll('.flyer-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Handle add flyer form submission
    document.getElementById('addFlyerForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

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
                    alert('Flyer added successfully!');
                    location.reload();
                } else {
                    alert('Failed to add flyer: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the flyer');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
    });

    // Handle edit flyer form submission
    document.getElementById('editFlyerForm').addEventListener('submit', function(e) {
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
                    alert('Flyer updated successfully!');
                    location.reload();
                } else {
                    alert('Failed to update flyer: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the flyer');
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
