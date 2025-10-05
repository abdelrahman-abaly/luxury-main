@extends("layouts.main")

@section("content")
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 dashboard-header">Out of Stock</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('warehouse.home')}}">Warehouse</a></li>
            <li class="breadcrumb-item">Stock</li>
            <li class="breadcrumb-item active">Out of Stock</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Out of Stock</h5>
                <h3 class="mb-1">{{number_format($stats['total_products'] ?? 0)}}</h3>
                <p class="mb-0">Products</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Lost Value</h5>
                <h3 class="mb-1">EGP {{number_format(($stats['total_value'] ?? 0) / 1000)}}K</h3>
                <p class="mb-0">Potential Sales</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Warehouses</h5>
                <h3 class="mb-1">{{count($stats['warehouses'] ?? [])}}</h3>
                <p class="mb-0">Affected</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-secondary text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Last Updated</h5>
                <h3 class="mb-1">{{$stats['last_updated'] ? \Carbon\Carbon::parse($stats['last_updated'])->format('M d') : 'N/A'}}</h3>
                <p class="mb-0">Recent Change</p>
            </div>
        </div>
    </div>
</div>

<!-- Top Controls -->
<div class="card mb-3 border-danger">
    <div class="card-body">
        <form method="GET" action="{{route('warehouse.out-of-stock')}}">
            <div class="row align-items-center mb-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search out of stock..." value="{{request('search')}}">
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
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="publish" {{request('status') == 'publish' ? 'selected' : ''}}>Published</option>
                        <option value="draft" {{request('status') == 'draft' ? 'selected' : ''}}>Draft</option>
                        <option value="pending" {{request('status') == 'pending' ? 'selected' : ''}}>Pending</option>
                    </select>
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
                    <input type="text" class="form-control" placeholder="Search by SKU" id="skuFilter">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="lastFeedingFilter">
                        <option value="">Last Feeding</option>
                        <option value="recent">Recently Out</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="older">Older than Month</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="demandFilter">
                        <option value="">Demand Level</option>
                        <option value="high">High Demand</option>
                        <option value="medium">Medium Demand</option>
                        <option value="low">Low Demand</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary" id="resetFilters">Reset Filters</button>
                </div>
                <div class="col-md-2">
                    <span class="text-danger"><strong>{{ $products->total() }} out of stock</strong></span>
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
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>Items Completely Out of Stock</h5>
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
                        <th>Stock Status</th>
                        <th>Days Out</th>
                        <th>Lost Sales</th>
                        <th>Unit Price</th>
                        <th>Demand Level</th>
                        <th>Last Feeding</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="table-danger">
                        <td><input type="checkbox" class="form-check-input stock-checkbox" value="{{ $product->id }}"></td>
                        <td><strong>{{ $product->sku }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center">
                                @php
                                $images = json_decode($product->images, true);
                                $firstImage = !empty($images) && is_array($images) ? $images[0]['url'] ?? '' : '';
                                @endphp
                                @if($firstImage)
                                <img src="{{ $firstImage }}" alt="{{ $images[0]['alt'] ?? 'Product' }}" class="me-2" width="40" height="40" style="object-fit: cover; border-radius: 4px; opacity: 0.5;">
                                @else
                                <img src="https://via.placeholder.com/40x40/dc3545/ffffff?text=OUT" class="me-2" width="40" height="40">
                                @endif
                                <span>{{ $product->name }}</span>
                            </div>
                        </td>
                        <td><span class="badge bg-primary">{{ ucfirst($product->status) }}</span></td>
                        <td>
                            <span class="badge bg-danger fs-6">0 units</span>
                        </td>
                        <td><span class="text-danger">{{ $product->updated_at->diffInDays(now()) }} days</span></td>
                        <td><span class="text-danger">N/A</span></td>
                        <td>EGP {{ number_format($product->normal_price, 2) }}</td>
                        <td><span class="badge bg-danger">High Demand</span></td>
                        <td>{{ $product->updated_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-danger" title="Emergency Order" data-bs-toggle="modal" data-bs-target="#emergencyOrderModal{{ $product->id }}">
                                    <i class="fas fa-bolt"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" title="Urgent Feeding" data-bs-toggle="modal" data-bs-target="#urgentFeedingModal{{ $product->id }}">
                                    <i class="fas fa-exclamation"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                            No out of stock products found
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
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Grid View - Out of Stock Items</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card grid-item border-danger">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/250x200/dc3545/ffffff?text=OUT" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-danger">Out of Stock</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">RLX-GMT-001</h6>
                        <p class="card-text">
                            <strong>Rolex GMT Master</strong><br>
                            <span class="badge bg-primary">Luxury</span><br>
                            <strong>Out for:</strong> <span class="text-danger">15 days</span><br>
                            <strong>Lost Sales:</strong> <span class="text-danger">5 orders</span><br>
                            <span class="badge bg-danger">High Demand</span>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-bold">EGP 25,000</span>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-danger btn-sm">Emergency</button>
                            <button class="btn btn-warning btn-sm">Urgent</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card grid-item border-danger">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/250x200/dc3545/ffffff?text=OUT" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-danger">Out of Stock</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">CRT-TAN-001</h6>
                        <p class="card-text">
                            <strong>Cartier Tank</strong><br>
                            <span class="badge bg-warning">Classic</span><br>
                            <strong>Out for:</strong> <span class="text-danger">8 days</span><br>
                            <strong>Lost Sales:</strong> <span class="text-danger">3 orders</span><br>
                            <span class="badge bg-warning">Medium Demand</span>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-bold">EGP 25,000</span>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-danger btn-sm">Emergency</button>
                            <button class="btn btn-warning btn-sm">Urgent</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card grid-item border-danger">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/250x200/dc3545/ffffff?text=OUT" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-danger">Critical</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">OMG-PLA-001</h6>
                        <p class="card-text">
                            <strong>Omega Planet Ocean</strong><br>
                            <span class="badge bg-info">Sport</span><br>
                            <strong>Out for:</strong> <span class="text-danger">22 days</span><br>
                            <strong>Lost Sales:</strong> <span class="text-danger">8 orders</span><br>
                            <span class="badge bg-danger">High Demand</span>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-bold">EGP 18,000</span>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-danger btn-sm">Emergency</button>
                            <button class="btn btn-warning btn-sm">Urgent</button>
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
        <span class="text-muted">Showing 1-12 of 12 items</span>
        <br>
        <strong class="text-danger">Total Lost Sales: 24 orders | Estimated Loss: EGP 500,000</strong>
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
        background-color: rgba(220, 53, 69, 0.15);
    }

    .table-warning {
        background-color: rgba(255, 193, 7, 0.15);
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

    // Emergency/Urgent Order functionality
    document.querySelectorAll('.btn-danger, .btn-warning, .btn-info').forEach(button => {
        button.addEventListener('click', function() {
            let action = '';
            let urgency = '';

            if (this.classList.contains('btn-danger')) {
                action = 'create emergency order';
                urgency = 'emergency';
            } else if (this.classList.contains('btn-warning')) {
                action = 'create urgent feeding request';
                urgency = 'urgent';
            } else if (this.classList.contains('btn-info')) {
                action = 'create standard feeding request';
                urgency = 'standard';
            }

            if (action && confirm(`Are you sure you want to ${action}?`)) {
                alert(`${urgency.charAt(0).toUpperCase() + urgency.slice(1)} request has been created successfully!`);
                // Here you would make an AJAX call to create the request
            }
        });
    });

    // Reset Filters
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('searchBox').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('skuFilter').value = '';
        document.getElementById('lastFeedingFilter').value = '';
        document.getElementById('demandFilter').value = '';
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