@extends("layouts.main")

@section("content")
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 dashboard-header">Almost Out of Stock</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('warehouse.home')}}">Warehouse</a></li>
            <li class="breadcrumb-item">Stock</li>
            <li class="breadcrumb-item active">Almost Out of Stock</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Low Stock Products</h5>
                <h3 class="mb-1">{{number_format($stats['total_products'] ?? 0)}}</h3>
                <p class="mb-0">Need Attention</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Critical Products</h5>
                <h3 class="mb-1">{{number_format($stats['critical_products'] ?? 0)}}</h3>
                <p class="mb-0">Urgent Restock</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Total Value</h5>
                <h3 class="mb-1">EGP {{number_format(($stats['total_value'] ?? 0) / 1000)}}K</h3>
                <p class="mb-0">At Risk</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Warehouses</h5>
                <h3 class="mb-1">{{count($stats['warehouses'] ?? [])}}</h3>
                <p class="mb-0">Affected</p>
            </div>
        </div>
    </div>
</div>

<!-- Top Controls -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{route('warehouse.almost-out-stock')}}">
            <div class="row align-items-center mb-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search low stock..." value="{{request('search')}}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="warehouse_id">
                        <option value="">All Warehouses</option>
                        @foreach($stats['warehouses'] ?? [] as $warehouse)
                        <option value="{{$warehouse->warehouse_id}}" {{request('warehouse_id') == $warehouse->warehouse_id ? 'selected' : ''}}>
                            Warehouse {{$warehouse->warehouse_id}} ({{$warehouse->count}})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="max_stock" placeholder="Max Stock" value="{{request('max_stock', 5)}}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                <div class="col-md-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="listViewBtn">
                            <i class="fas fa-list"></i> List View
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="gridViewBtn">
                            <i class="fas fa-th-large"></i> Grid View
                        </button>
                    </div>
                </div>
            </div>

            <!-- Advanced Filters Row -->
            <div class="row align-items-center">
                <div class="col-md-2">
                    <select class="form-select" id="stockFilter">
                        <option value="">Stock Level</option>
                        <option value="critical">Critical (1-2)</option>
                        <option value="very_low">Very Low (3-5)</option>
                        <option value="low">Low (6-10)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" placeholder="Search by SKU" id="skuFilter">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="lastFeedingFilter">
                        <option value="">Last Feeding</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="older">Older than Month</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary" id="resetFilters">Reset Filters</button>
                </div>
                <div class="col-md-2">
                    <span class="text-danger"><strong>{{ $products->total() }} low stock items</strong></span>
                </div>
            </div>

            <!-- Bulk Actions Row -->
            <div class="row align-items-center mt-3 bg-light p-3 rounded">
                <div class="col-md-3">
                    <select class="form-select" id="bulkAction">
                        <option value="">Bulk Actions</option>
                        <option value="mark_restock">🛒 Mark for Restock</option>
                        <option value="export">📥 Export Selected</option>
                        <option value="print">🖨️ Print Labels</option>
                        <option value="update_status">✏️ Update Status</option>
                        <option value="set_priority">⚡ Set Priority</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" id="submitBulk" disabled>
                        <i class="fas fa-check"></i> Submit
                    </button>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-success" id="selectAllBtn">
                        <i class="fas fa-check-square"></i> Select All
                    </button>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary" id="deselectAllBtn">
                        <i class="fas fa-square"></i> Deselect All
                    </button>
                </div>
                <div class="col-md-3">
                    <span class="text-muted fw-bold" id="selectedCount">0 items selected</span>
                </div>
            </div>
    </div>
</div>

<!-- List View -->
<div class="card" id="listView">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Items Running Low on Stock</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>SKU</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Min. Stock</th>
                        <th>Alert Level</th>
                        <th>Priority</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                        <th>Last Feeding</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="{{ $product->stock_quantity <= 2 ? 'table-danger' : 'table-warning' }}">
                        <td><input type="checkbox" class="form-check-input stock-checkbox" value="{{ $product->id }}"></td>
                        <td><strong>{{ $product->sku }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center">
                                @php
                                $images = json_decode($product->images, true);
                                $firstImage = !empty($images) && is_array($images) ? $images[0]['url'] ?? '' : '';
                                @endphp
                                @if($firstImage)
                                <img src="{{ $firstImage }}" alt="{{ $images[0]['alt'] ?? 'Product' }}" class="me-2" width="40" height="40" style="object-fit: cover; border-radius: 4px;">
                                @else
                                <img src="https://via.placeholder.com/40x40/dc3545/ffffff?text={{ substr($product->name, 0, 3) }}" class="me-2" width="40" height="40">
                                @endif
                                <span>{{ $product->name }}</span>
                            </div>
                        </td>
                        <td><span class="badge bg-primary">{{ ucfirst($product->status) }}</span></td>
                        <td>
                            <span class="badge {{ $product->stock_quantity <= 2 ? 'bg-danger' : 'bg-warning' }} fs-6">{{ $product->stock_quantity }} unit{{ $product->stock_quantity != 1 ? 's' : '' }}</span>
                        </td>
                        <td>5</td>
                        <td><span class="badge {{ $product->stock_quantity <= 2 ? 'bg-danger' : 'bg-warning' }}">{{ $product->stock_quantity <= 2 ? 'Critical' : 'Low' }}</span></td>
                        <td>
                            @if($product->stock_quantity <= 1)
                                <span class="badge bg-danger fs-6">
                                <i class="fas fa-exclamation-triangle"></i> URGENT
                                </span>
                                @elseif($product->stock_quantity <= 2)
                                    <span class="badge bg-warning fs-6">
                                    <i class="fas fa-exclamation"></i> HIGH
                                    </span>
                                    @elseif($product->stock_quantity <= 3)
                                        <span class="badge bg-info fs-6">
                                        <i class="fas fa-info-circle"></i> MEDIUM
                                        </span>
                                        @else
                                        <span class="badge bg-secondary fs-6">
                                            <i class="fas fa-info"></i> LOW
                                        </span>
                                        @endif
                        </td>
                        <td>EGP {{ number_format($product->normal_price, 2) }}</td>
                        <td><strong>EGP {{ number_format($product->stock_quantity * $product->normal_price, 2) }}</strong></td>
                        <td>{{ $product->updated_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-danger" title="Urgent Request" data-bs-toggle="modal" data-bs-target="#urgentRestockModal{{ $product->id }}">
                                    <i class="fas fa-exclamation"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" title="Request Feeding" data-bs-toggle="modal" data-bs-target="#requestFeedingModal{{ $product->id }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                            No low stock products found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Grid View -->
<div class="card d-none" id="gridView">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Grid View - Almost Out of Stock</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card grid-item border-danger">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/250x200/dc3545/ffffff?text=RLX" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-danger">Critical</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">RLX-DAY-001</h6>
                        <p class="card-text">
                            <strong>Rolex Daytona</strong><br>
                            <span class="badge bg-primary">Luxury</span><br>
                            <strong>Stock:</strong> <span class="text-danger">1 unit</span><br>
                            <strong>Min:</strong> 5 units
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-bold">EGP 35,000</span>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-danger btn-sm">Urgent</button>
                            <button class="btn btn-warning btn-sm">Request</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card grid-item border-warning">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/250x200/fd7e14/ffffff?text=PAT" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-warning">Very Low</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">PAT-NAU-001</h6>
                        <p class="card-text">
                            <strong>Patek Philippe Nautilus</strong><br>
                            <span class="badge bg-primary">Luxury</span><br>
                            <strong>Stock:</strong> <span class="text-warning">3 units</span><br>
                            <strong>Min:</strong> 10 units
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-bold">EGP 135,000</span>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-warning btn-sm">Request</button>
                            <button class="btn btn-outline-primary btn-sm">View</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card grid-item border-warning">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/250x200/34495e/ffffff?text=BRE" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-warning">Very Low</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">BRE-SUP-001</h6>
                        <p class="card-text">
                            <strong>Breitling Superocean</strong><br>
                            <span class="badge bg-info">Sport</span><br>
                            <strong>Stock:</strong> <span class="text-warning">4 units</span><br>
                            <strong>Min:</strong> 8 units
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-bold">EGP 72,000</span>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-warning btn-sm">Request</button>
                            <button class="btn btn-outline-primary btn-sm">View</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pagination and Summary -->
<div class="d-flex justify-content-between align-items-center mt-4">
    <div>
        <span class="text-muted">Showing 1-18 of 18 items</span>
        <br>
        <strong class="text-danger">Critical Items Need Immediate Attention: 3</strong>
    </div>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item">
                <a class="page-link" href="#">Next</a>
            </li>
        </ul>
    </nav>
</div>

<style>
    .dashboard-header {
        color: #2c3e50;
        font-weight: 600;
    }

    .breadcrumb {
        background-color: transparent;
        margin-bottom: 0;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .grid-item {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .grid-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .table th {
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    /* Priority Badge Styles */
    .badge.fs-6 {
        font-size: 0.8em !important;
        padding: 0.4em 0.6em;
        border-radius: 0.375rem;
    }

    .badge.bg-danger.fs-6 {
        background-color: #dc3545 !important;
        animation: pulse 2s infinite;
    }

    .badge.bg-warning.fs-6 {
        background-color: #ffc107 !important;
        color: #000 !important;
    }

    .badge.bg-info.fs-6 {
        background-color: #0dcaf0 !important;
        color: #000 !important;
    }

    .badge.bg-secondary.fs-6 {
        background-color: #6c757d !important;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }

        100% {
            opacity: 1;
        }
    }

    .badge {
        font-size: 0.75em;
    }

    .btn-group .btn.active {
        background-color: #0d6efd;
        color: white;
    }

    .fs-6 {
        font-size: 0.9rem !important;
        padding: 0.4rem 0.8rem;
    }

    .table-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .table-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }
</style>

<script>
    // View Toggle
    document.getElementById('listViewBtn').addEventListener('click', function() {
        document.getElementById('listView').classList.remove('d-none');
        document.getElementById('gridView').classList.add('d-none');
        this.classList.add('active');
        document.getElementById('gridViewBtn').classList.remove('active');
    });

    document.getElementById('gridViewBtn').addEventListener('click', function() {
        document.getElementById('gridView').classList.remove('d-none');
        document.getElementById('listView').classList.add('d-none');
        this.classList.add('active');
        document.getElementById('listViewBtn').classList.remove('active');
    });

    // Select All Functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.stock-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Request Feeding functionality
    document.querySelectorAll('.btn-danger, .btn-warning, .btn-info').forEach(button => {
        button.addEventListener('click', function() {
            let action = '';
            let urgency = '';

            if (this.classList.contains('btn-danger')) {
                action = 'create urgent feeding request';
                urgency = 'urgent';
            } else if (this.classList.contains('btn-warning')) {
                action = 'create feeding request';
                urgency = 'normal';
            } else if (this.classList.contains('btn-info')) {
                action = 'create feeding request';
                urgency = 'low priority';
            }

            if (action && confirm(`Are you sure you want to ${action}?`)) {
                alert(`${urgency.charAt(0).toUpperCase() + urgency.slice(1)} feeding request has been created successfully!`);
                // Here you would make an AJAX call to create the feeding request
            }
        });
    });

    // Reset Filters
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('searchBox').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('stockFilter').value = '';
        document.getElementById('skuFilter').value = '';
        document.getElementById('lastFeedingFilter').value = '';
        console.log('Filters reset');
    });

    // Bulk Actions
    document.getElementById('submitBulk').addEventListener('click', function() {
        const action = document.getElementById('bulkAction').value;
        const selectedItems = document.querySelectorAll('.stock-checkbox:checked');

        if (!action) {
            alert('Please select a bulk action');
            return;
        }

        if (selectedItems.length === 0) {
            alert('Please select at least one item');
            return;
        }

        if (action === 'urgent_order') {
            alert(`Urgent feeding requests will be created for ${selectedItems.length} selected items`);
        } else {
            alert(`${action} will be performed on ${selectedItems.length} selected items`);
        }
    });

    // Bulk Actions
    document.getElementById('submitBulk').addEventListener('click', function() {
        const action = document.getElementById('bulkAction').value;
        const selectedItems = document.querySelectorAll('.stock-checkbox:checked');

        if (!action) {
            alert('Please select a bulk action');
            return;
        }

        if (selectedItems.length === 0) {
            alert('Please select at least one item');
            return;
        }

        if (action === 'mark_restock') {
            if (confirm(`Mark ${selectedItems.length} selected items for restock?`)) {
                alert(`Successfully marked ${selectedItems.length} items for restock!`);
                // Here you would make an AJAX call to update the products
            }
        } else if (action === 'export') {
            alert(`Exporting ${selectedItems.length} selected items...`);
            // Here you would trigger the export functionality
        } else if (action === 'print') {
            alert(`Printing labels for ${selectedItems.length} selected items...`);
            // Here you would trigger the print functionality
        } else if (action === 'update_status') {
            const newStatus = prompt('Enter new status (publish/draft/pending):');
            if (newStatus && ['publish', 'draft', 'pending'].includes(newStatus)) {
                alert(`Updating status to "${newStatus}" for ${selectedItems.length} selected items...`);
                // Here you would make an AJAX call to update the status
            }
        } else if (action === 'set_priority') {
            const priority = prompt('Enter priority level (urgent/high/medium/low):');
            if (priority && ['urgent', 'high', 'medium', 'low'].includes(priority)) {
                alert(`Setting priority to "${priority}" for ${selectedItems.length} selected items...`);
                // Here you would make an AJAX call to update the priority
            }
        } else {
            alert(`${action} will be performed on ${selectedItems.length} selected items`);
        }
    });

    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.stock-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    // Select All Button
    document.getElementById('selectAllBtn').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.stock-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        document.getElementById('selectAll').checked = true;
        updateSelectedCount();
    });

    // Deselect All Button
    document.getElementById('deselectAllBtn').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.stock-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('selectAll').checked = false;
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        const selectedItems = document.querySelectorAll('.stock-checkbox:checked');
        const count = selectedItems.length;
        document.getElementById('selectedCount').textContent = `${count} items selected`;

        // Update submit button state
        const submitBtn = document.getElementById('submitBulk');
        if (count > 0) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-secondary');
        }
    }

    // Add event listeners to all checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('stock-checkbox')) {
            updateSelectedCount();
        }
    });

    // Search functionality (demo)
    document.getElementById('searchBox').addEventListener('input', function() {
        console.log('Searching for:', this.value);
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSelectedCount();
    });
</script>
@endsection
