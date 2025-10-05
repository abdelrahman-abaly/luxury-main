@extends("layouts.main")

@section("content")
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 dashboard-header">Waiting for Purchases</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('warehouse.home')}}">Warehouse</a></li>
            <li class="breadcrumb-item active">Waiting for Purchases</li>
        </ol>
    </nav>
</div>

<!-- Top Controls -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search orders..." id="searchBox">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="bulkAction">
                    <option value="">Bulk Actions</option>
                    <option value="purchase">Mark for Purchase</option>
                    <option value="export">Export Selected</option>
                    <option value="print">Print Selected</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" id="submitBulk">Submit</button>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterBy">
                    <option value="">Filter by</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="urgent">Urgent Orders</option>
                </select>
            </div>
        </div>

        <div class="row mt-3 align-items-center">
            <div class="col-md-6">
                <div class="text-muted mb-2">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Orders with Out of Stock Products:</strong>
                </div>
                <div class="text-dark">
                    <span class="badge bg-warning me-2">{{ $orders->total() }} Orders</span>
                    <span class="badge bg-danger me-2">{{ $totalItems ?? 0 }} Out of Stock Items</span>
                    <span class="badge bg-info">EGP {{ number_format($totalPrice ?? 0, 2) }}</span>
                </div>
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
    </div>
</div>

<!-- List View -->
<div class="card" id="listView">
    <div class="card-header">
        <h5 class="mb-0">Orders Waiting for Purchase Approval</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Created By</th>
                        <th>Comments</th>
                        <th>Date</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Government</th>
                        <th>SKU</th>
                        <th>Product</th>
                        <th>Required Qty</th>
                        <th>Current Stock</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    @foreach($order->products->where('stock_quantity', '0') as $product)
                    <tr>
                        <td><input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($order->employee && $order->employee->avatar)
                                <img src="{{ asset('storage/' . $order->employee->avatar) }}" class="rounded-circle me-2" width="30" height="30" alt="Employee Avatar">
                                @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($order->employee ? $order->employee->name : 'Unknown') }}&background=007bff&color=fff" class="rounded-circle me-2" width="30" height="30" alt="Employee Avatar">
                                @endif
                                <span>{{ $order->employee ? $order->employee->name : 'Unknown' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex">
                                <span class="badge bg-info me-1" title="Internal Comments">
                                    <i class="fas fa-comment"></i> 0
                                </span>
                                <span class="badge bg-warning" title="Client Comments">
                                    <i class="fas fa-comment-dots"></i> {{ $order->notes ? 1 : 0 }}
                                </span>
                            </div>
                        </td>
                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        <td><strong>#{{ $order->order_number }}</strong></td>
                        <td>{{ $order->customer ? $order->customer->name : 'Unknown Customer' }}</td>
                        <td>{{ $order->customer ? $order->customer->phone_numbers : 'N/A' }}</td>
                        <td>{{ $order->governorate ?? 'N/A' }}</td>
                        <td>{{ $product->sku ?? 'N/A' }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($product->images)
                                @php
                                $images = json_decode($product->images, true);
                                $firstImage = is_array($images) && count($images) > 0 ? $images[0] : $product->images;
                                @endphp
                                @if($firstImage)
                                @if(str_starts_with($firstImage, 'http'))
                                <img src="{{ $firstImage }}" class="me-2" width="40" height="40" alt="Product Image" style="object-fit: cover;">
                                @else
                                <img src="{{ asset('storage/' . $firstImage) }}" class="me-2" width="40" height="40" alt="Product Image" style="object-fit: cover;">
                                @endif
                                @else
                                <img src="https://via.placeholder.com/40x40/dc3545/ffffff?text={{ substr($product->name, 0, 3) }}" class="me-2" width="40" height="40" alt="Product Image">
                                @endif
                                @else
                                <img src="https://via.placeholder.com/40x40/dc3545/ffffff?text={{ substr($product->name, 0, 3) }}" class="me-2" width="40" height="40" alt="Product Image">
                                @endif
                                <span>{{ $product->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-warning">{{ $product->pivot->quantity ?? 1 }}</span>
                        </td>
                        <td>
                            <span class="badge bg-danger">{{ $product->stock_quantity }}</span>
                        </td>
                        <td>EGP {{ number_format($product->normal_price, 2) }}</td>
                        <td>
                            <span class="badge bg-warning">Needs Purchase</span>
                        </td>
                    </tr>
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="14" class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                            <strong>Great! No out of stock products found.</strong><br>
                            <small>All products are in stock and ready for fulfillment.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} results
                </div>
                <nav aria-label="Page navigation">
                    {{ $orders->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Grid View -->
<div class="card d-none" id="gridView">
    <div class="card-header">
        <h5 class="mb-0">Grid View - Out of Stock Products</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($orders as $order)
            @foreach($order->products->where('stock_quantity', '0') as $product)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card grid-item">
                    <div class="position-relative">
                        @if($product->images)
                        @php
                        $images = json_decode($product->images, true);
                        $firstImage = is_array($images) && count($images) > 0 ? $images[0] : $product->images;
                        @endphp
                        @if($firstImage)
                        @if(str_starts_with($firstImage, 'http'))
                        <img src="{{ $firstImage }}" class="card-img-top" alt="Product Image" style="height: 200px; object-fit: cover;">
                        @else
                        <img src="{{ asset('storage/' . $firstImage) }}" class="card-img-top" alt="Product Image" style="height: 200px; object-fit: cover;">
                        @endif
                        @else
                        <img src="https://via.placeholder.com/300x200/dc3545/ffffff?text={{ substr($product->name, 0, 3) }}" class="card-img-top" alt="Product Image">
                        @endif
                        @else
                        <img src="https://via.placeholder.com/300x200/dc3545/ffffff?text={{ substr($product->name, 0, 3) }}" class="card-img-top" alt="Product Image">
                        @endif
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-danger">Out of Stock</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">
                            SKU: {{ $product->sku ?? 'N/A' }}
                        </h6>
                        <p class="card-text">
                            <strong>Order #:</strong> #{{ $order->order_number }}<br>
                            <strong>Customer:</strong> {{ $order->customer ? $order->customer->name : 'Unknown Customer' }}<br>
                            <strong>Required Qty:</strong> <span class="badge bg-warning">{{ $product->pivot->quantity ?? 1 }}</span>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold">EGP {{ number_format($product->normal_price, 2) }}</span>
                            <span class="badge bg-warning">Needs Purchase</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @empty
            <div class="col-12">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i><br>
                    <h5>Great! No out of stock products found.</h5>
                    <p>All products are in stock and ready for fulfillment.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
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

    .expandable-row {
        cursor: pointer;
    }

    .expandable-row:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }

    .expand-icon {
        transition: transform 0.3s ease;
    }

    .expandable-row[aria-expanded="true"] .expand-icon {
        transform: rotate(90deg);
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

    /* Pagination Styling */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        color: #0d6efd;
        border: 1px solid #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .pagination .page-link:hover {
        color: #0a58ca;
        background-color: #e9ecef;
    }

    .card-footer {
        background-color: #f8f9fa;
        padding: 0.75rem 1.25rem;
    }
</style>

<script>
    // View Toggle Only
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
        const checkboxes = document.querySelectorAll('.order-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Bulk Actions
    document.getElementById('submitBulk').addEventListener('click', function() {
        const action = document.getElementById('bulkAction').value;
        const selectedOrders = document.querySelectorAll('.order-checkbox:checked');

        if (!action) {
            alert('Please select a bulk action');
            return;
        }

        if (selectedOrders.length === 0) {
            alert('Please select at least one order');
            return;
        }

        alert(`${action} action will be performed on ${selectedOrders.length} selected orders`);
    });

    // Search functionality (demo)
    document.getElementById('searchBox').addEventListener('input', function() {
        console.log('Searching for:', this.value);
    });

    // Filter functionality (demo)
    document.getElementById('filterBy').addEventListener('change', function() {
        console.log('Filtering by:', this.value);
    });
</script>
@endsection