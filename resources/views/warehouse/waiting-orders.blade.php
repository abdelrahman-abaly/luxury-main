@extends("layouts.main")

@section("content")
<!-- Success/Error Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 dashboard-header">Waiting Orders</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('warehouse.home')}}">Warehouse</a></li>
            <li class="breadcrumb-item active">Waiting Orders</li>
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
                    <option value="accept">Accept Selected</option>
                    <option value="reject">Reject Selected</option>
                    <option value="export">Export Selected</option>
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
                    <option value="priority">High Priority</option>
                </select>
            </div>
        </div>

        <div class="row mt-3 align-items-center">
            <div class="col-md-6">
                <span class="text-muted">Total: <strong>{{ $orders->total() }} items</strong></span>
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
        <h5 class="mb-0">Orders Waiting for Processing</h5>
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
                        <th>Action</th>
                        <th>Date</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Government</th>
                        <th>SKU</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
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
                        <td>
                            <form method="POST" action="{{ route('warehouse.accept-order', $order->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Accept</button>
                            </form>
                            <form method="POST" action="{{ route('warehouse.reject-order', $order->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                            </form>
                        </td>
                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        <td><strong>#{{ $order->order_number }}</strong></td>
                        <td>{{ $order->customer ? $order->customer->name : 'Unknown Customer' }}</td>
                        <td>{{ $order->customer ? $order->customer->phone_numbers : 'N/A' }}</td>
                        <td>{{ $order->governorate ?? 'N/A' }}</td>
                        <td>
                            @if($order->products->isNotEmpty())
                            {{ $order->products->first()->sku ?? 'N/A' }}
                            @else
                            N/A
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($order->products->isNotEmpty() && $order->products->first()->images)
                                @php
                                $images = json_decode($order->products->first()->images, true);
                                $firstImage = is_array($images) && count($images) > 0 ? $images[0] : $order->products->first()->images;
                                @endphp
                                @if($firstImage)
                                @if(str_starts_with($firstImage, 'http'))
                                <img src="{{ $firstImage }}" class="me-2" width="40" height="40" alt="Product Image" style="object-fit: cover;">
                                @else
                                <img src="{{ asset('storage/' . $firstImage) }}" class="me-2" width="40" height="40" alt="Product Image" style="object-fit: cover;">
                                @endif
                                @else
                                <img src="https://via.placeholder.com/40x40/007bff/ffffff?text={{ substr($order->products->first()->name, 0, 3) }}" class="me-2" width="40" height="40" alt="Product Image">
                                @endif
                                @else
                                <img src="https://via.placeholder.com/40x40/007bff/ffffff?text=ORD" class="me-2" width="40" height="40" alt="Product Image">
                                @endif
                                <span>{{ $order->products->isNotEmpty() ? $order->products->first()->name : 'Order Items' }}</span>
                            </div>
                        </td>
                        <td>EGP {{ number_format($order->total, 2) }}</td>
                        <td>
                            @if($order->products->isNotEmpty())
                            @php
                            $product = $order->products->first();
                            $stockStatus = $product->stock_quantity > 0 ? 'Available' : 'Out of Stock';
                            $badgeClass = $product->stock_quantity > 0 ? 'bg-success' : 'bg-danger';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $stockStatus }}</span>
                            @else
                            <span class="badge bg-warning">Unknown</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            No waiting orders found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
                </div>
                <div>
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Grid View -->
<div class="card d-none" id="gridView">
    <div class="card-header">
        <h5 class="mb-0">Grid View - Orders Waiting for Processing</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($orders as $order)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card grid-item">
                    <div class="position-relative">
                        @if($order->products->isNotEmpty() && $order->products->first()->images)
                        @php
                        $images = json_decode($order->products->first()->images, true);
                        $firstImage = is_array($images) && count($images) > 0 ? $images[0] : $order->products->first()->images;
                        @endphp
                        @if($firstImage)
                        @if(str_starts_with($firstImage, 'http'))
                        <img src="{{ $firstImage }}" class="card-img-top" alt="Product Image" style="height: 200px; object-fit: cover;">
                        @else
                        <img src="{{ asset('storage/' . $firstImage) }}" class="card-img-top" alt="Product Image" style="height: 200px; object-fit: cover;">
                        @endif
                        @else
                        <img src="https://via.placeholder.com/300x200/007bff/ffffff?text={{ substr($order->products->first()->name, 0, 3) }}" class="card-img-top" alt="Product Image">
                        @endif
                        @else
                        <img src="https://via.placeholder.com/300x200/007bff/ffffff?text=ORD" class="card-img-top" alt="Product Image">
                        @endif
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}">
                        </div>
                        @if($order->products->isNotEmpty())
                        @php
                        $product = $order->products->first();
                        $stockStatus = $product->stock_quantity > 0 ? 'Available' : 'Out of Stock';
                        $badgeClass = $product->stock_quantity > 0 ? 'bg-success' : 'bg-danger';
                        @endphp
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge {{ $badgeClass }}">{{ $stockStatus }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">
                            SKU: {{ $order->products->isNotEmpty() ? ($order->products->first()->sku ?? 'N/A') : 'N/A' }}
                        </h6>
                        <p class="card-text">
                            <strong>Order #:</strong> #{{ $order->order_number }}<br>
                            <strong>Customer:</strong> {{ $order->customer ? $order->customer->name : 'Unknown Customer' }}<br>
                            <strong>Type:</strong> <span class="badge bg-primary">Individual</span>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold">EGP {{ number_format($order->total, 2) }}</span>
                            <form method="POST" action="{{ route('warehouse.accept-order', $order->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Accept</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                    No waiting orders found
                </div>
            </div>
            @endforelse
        </div>

        <!-- Grid View Pagination -->
        @if($orders->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
                </div>
                <div>
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Summary and Global Pagination -->
<div class="card mt-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="text-muted mb-2">
                    <i class="fas fa-chart-bar me-1"></i>
                    <strong>Summary:</strong>
                </div>
                <div class="text-dark">
                    <span class="badge bg-primary me-2">{{ $totalItems ?? 0 }} Items</span>
                    <span class="badge bg-success">EGP {{ number_format($totalPrice ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="col-md-6">
                @if($orders->hasPages())
                <div class="d-flex justify-content-end">
                    <nav aria-label="Page navigation">
                        {{ $orders->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
                @endif
            </div>
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

    /* Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        color: #0d6efd;
        border-color: #dee2e6;
        padding: 0.5rem 0.75rem;
    }

    .pagination .page-link:hover {
        color: #0a58ca;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }

    .card-footer.bg-light {
        background-color: #f8f9fa !important;
        border-top: 1px solid #dee2e6;
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
        const checkboxes = document.querySelectorAll('.order-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Expandable Rows
    document.querySelectorAll('.expandable-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox' && e.target.tagName !== 'BUTTON') {
                const target = this.getAttribute('data-bs-target');
                const collapse = document.querySelector(target);
                const icon = this.querySelector('.expand-icon');

                if (collapse.classList.contains('show')) {
                    collapse.classList.remove('show');
                    this.setAttribute('aria-expanded', 'false');
                } else {
                    collapse.classList.add('show');
                    this.setAttribute('aria-expanded', 'true');
                }
            }
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
        // This would typically filter the table rows
        console.log('Searching for:', this.value);
    });

    // Filter functionality (demo)
    document.getElementById('filterBy').addEventListener('change', function() {
        console.log('Filtering by:', this.value);
    });
</script>
@endsection