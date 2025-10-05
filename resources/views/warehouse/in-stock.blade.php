@extends("layouts.main")

@section("content")
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 dashboard-header">In Stock</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('warehouse.home')}}">Warehouse</a></li>
            <li class="breadcrumb-item active">In Stock</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Total Products</h5>
                <h3 class="mb-1">{{number_format($stats['total_products'] ?? 0)}}</h3>
                <p class="mb-0">In Stock</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Total Value</h5>
                <h3 class="mb-1">EGP {{number_format(($stats['total_value'] ?? 0) / 1000)}}K</h3>
                <p class="mb-0">Inventory Value</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Average Stock</h5>
                <h3 class="mb-1">{{number_format($stats['average_stock'] ?? 0, 1)}}</h3>
                <p class="mb-0">Per Product</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Warehouses</h5>
                <h3 class="mb-1">{{count($stats['warehouses'] ?? [])}}</h3>
                <p class="mb-0">Active Locations</p>
            </div>
        </div>
    </div>
</div>

<!-- Top Controls -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{route('warehouse.in-stock')}}">
            <div class="row align-items-center mb-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search products..." value="{{request('search')}}">
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
                    <input type="number" class="form-control" name="min_stock" placeholder="Min Stock" value="{{request('min_stock')}}">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="max_stock" placeholder="Max Stock" value="{{request('max_stock')}}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="row align-items-center">
            <div class="col-md-6">
                <span class="text-muted">Total: <strong>{{ $products->total() }} products</strong></span>
            </div>
            <div class="col-md-6 text-end">
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
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-success" id="exportBtn">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="bulkUpdateBtn">
                        <i class="fas fa-edit"></i> Bulk Update
                    </button>
                    <button type="button" class="btn btn-outline-info" id="printLabelsBtn">
                        <i class="fas fa-print"></i> Print Labels
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- List View -->
<div class="card" id="listView">
    <div class="card-header">
        <h5 class="mb-0">Available Stock Items</h5>
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
                        <th>Max. Stock</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                        <th>Last Feeding</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
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
                                <img src="https://via.placeholder.com/40x40/007bff/ffffff?text={{ substr($product->name, 0, 3) }}" class="me-2" width="40" height="40">
                                @endif
                                <span>{{ $product->name }}</span>
                            </div>
                        </td>
                        <td><span class="badge bg-primary">{{ ucfirst($product->status) }}</span></td>
                        <td>
                            <span class="badge bg-success fs-6">{{ $product->stock_quantity }} units</span>
                        </td>
                        <td>5</td>
                        <td>50</td>
                        <td>EGP {{ number_format($product->normal_price, 2) }}</td>
                        <td><strong>EGP {{ number_format($product->stock_quantity * $product->normal_price, 2) }}</strong></td>
                        <td>{{ $product->updated_at->format('Y-m-d') }}</td>
                        <td>W{{ $product->warehouse_id ?? 'N/A' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary" title="View Details" data-bs-toggle="modal" data-bs-target="#productModal{{ $product->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Update Stock" data-bs-toggle="modal" data-bs-target="#updateStockModal{{ $product->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-2x mb-2"></i><br>
                            No products in stock found
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
    <div class="card-header">
        <h5 class="mb-0">Grid View - In Stock Items</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card grid-item">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/250x200/007bff/ffffff?text=RLX" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-success">25 units</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">RLX-SUB-001</h6>
                        <p class="card-text">
                            <strong>Rolex Submariner</strong><br>
                            <span class="badge bg-primary">Luxury</span><br>
                            <strong>Location:</strong> A1-B2-C3
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-bold">EGP 375,000</span>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-outline-primary btn-sm">View</button>
                            <button class="btn btn-outline-success btn-sm">Update</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card grid-item">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/250x200/28a745/ffffff?text=OMG" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-success">18 units</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">OMG-SPD-001</h6>
                        <p class="card-text">
                            <strong>Omega Speedmaster</strong><br>
                            <span class="badge bg-info">Sport</span><br>
                            <strong>Location:</strong> A2-B1-C4
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-bold">EGP 225,000</span>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-outline-primary btn-sm">View</button>
                            <button class="btn btn-outline-success btn-sm">Update</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card grid-item">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/250x200/6f42c1/ffffff?text=TAG" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-warning">8 units</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">TAG-F1-001</h6>
                        <p class="card-text">
                            <strong>TAG Heuer Formula 1</strong><br>
                            <span class="badge bg-info">Sport</span><br>
                            <strong>Location:</strong> B1-C2-D1
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-bold">EGP 64,000</span>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-outline-primary btn-sm">View</button>
                            <button class="btn btn-outline-success btn-sm">Update</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card grid-item">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/250x200/dc3545/ffffff?text=BOX" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-success">45 units</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">BOX-LUX-001</h6>
                        <p class="card-text">
                            <strong>Luxury Watch Box</strong><br>
                            <span class="badge bg-secondary">Accessories</span><br>
                            <strong>Location:</strong> C1-D2-E1
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-bold">EGP 22,500</span>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-outline-primary btn-sm">View</button>
                            <button class="btn btn-outline-success btn-sm">Update</button>
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
        <span class="text-muted">Showing 1-20 of 45 items</span>
        <br>
        <strong>Total Stock Value: EGP 986,500</strong>
    </div>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
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

    // Reset Filters
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('searchBox').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('stockFilter').value = '';
        document.getElementById('skuFilter').value = '';
        document.getElementById('countFilter').value = '';
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

        alert(`${action} action will be performed on ${selectedItems.length} selected items`);
    });

    // Filter functionality (demo)
    document.getElementById('categoryFilter').addEventListener('change', function() {
        console.log('Category filter:', this.value);
    });

    document.getElementById('stockFilter').addEventListener('change', function() {
        console.log('Stock filter:', this.value);
    });

    document.getElementById('skuFilter').addEventListener('input', function() {
        console.log('SKU filter:', this.value);
    });

    document.getElementById('countFilter').addEventListener('change', function() {
        console.log('Count filter:', this.value);
    });

    document.getElementById('lastFeedingFilter').addEventListener('change', function() {
        console.log('Last feeding filter:', this.value);
    });

    // Search functionality (demo)
    document.getElementById('searchBox').addEventListener('input', function() {
        console.log('Searching for:', this.value);
    });
</script>
@endsection
