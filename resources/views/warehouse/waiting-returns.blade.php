@extends("layouts.main")

@section("content")
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 dashboard-header">Waiting Returns</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('warehouse.home')}}">Warehouse</a></li>
            <li class="breadcrumb-item active">Waiting Returns</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Waiting Returns</h5>
                <h3 class="mb-1">{{number_format($stats['total_returns'] ?? 0)}}</h3>
                <p class="mb-0">Pending Review</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Urgent Returns</h5>
                <h3 class="mb-1">{{number_format($stats['urgent_returns'] ?? 0)}}</h3>
                <p class="mb-0">Over 3 Days</p>
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
                <h5 class="card-title">Avg Processing</h5>
                <h3 class="mb-1">{{number_format($stats['avg_processing_time'] ?? 0, 1)}}h</h3>
                <p class="mb-0">Time</p>
            </div>
        </div>
    </div>
</div>

<!-- Top Controls -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{route('warehouse.waiting-returns')}}">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search returns..." value="{{request('search')}}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" placeholder="From Date" value="{{request('date_from')}}">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" placeholder="To Date" value="{{request('date_to')}}">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="reason" placeholder="Reason" value="{{request('reason')}}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{route('warehouse.waiting-returns')}}" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh"></i>
                    </a>
                </div>
            </div>
        </form>

        <div class="row mt-3 align-items-center">
            <div class="col-md-6">
                <span class="text-muted">Total: <strong>{{ $returns->total() }} items</strong></span>
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
        <h5 class="mb-0">Returns Waiting for Processing</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Total Value</th>
                        <th>Return Reason</th>
                        <th>Days Waiting</th>
                        <th>Priority</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    <tr class="{{ $return->created_at->diffInDays(now()) > 3 ? 'table-danger' : 'table-warning' }}">
                        <td><input type="checkbox" class="form-check-input order-checkbox" value="{{ $return->id }}"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <strong>{{ $return->order_number }}</strong>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($return->customer)
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($return->customer->name) }}&background=007bff&color=fff" class="rounded-circle me-2" width="30" height="30">
                                <span>{{ $return->customer->name }}</span>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($return->customer)
                            {{ $return->customer->phone }}
                            @else
                            <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $return->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $return->status)) }}</span>
                        </td>
                        <td><strong>EGP {{ number_format($return->total, 2) }}</strong></td>
                        <td>
                            <span class="text-muted">{{ Str::limit($return->notes, 30) }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $return->created_at->diffInDays(now()) > 3 ? 'bg-danger' : 'bg-warning' }}">
                                {{ $return->created_at->diffInDays(now()) }} days
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $return->created_at->diffInDays(now()) > 3 ? 'bg-danger' : 'bg-warning' }}">
                                {{ $return->created_at->diffInDays(now()) > 3 ? 'High' : 'Normal' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-success" title="Approve Return" data-bs-toggle="modal" data-bs-target="#approveReturnModal{{ $return->id }}">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" title="Reject Return" data-bs-toggle="modal" data-bs-target="#rejectReturnModal{{ $return->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-sm btn-info" title="View Details" data-bs-toggle="modal" data-bs-target="#returnDetailsModal{{ $return->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                            No waiting returns found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($returns->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $returns->firstItem() }} to {{ $returns->lastItem() }} of {{ $returns->total() }} results
                </div>
                <div>
                    {{ $returns->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Grid View -->
<div class="card d-none" id="gridView">
    <div class="card-header">
        <h5 class="mb-0">Grid View - Waiting Returns</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card grid-item">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/300x200/e67e22/ffffff?text=RLX" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-warning">Defective</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">SKU: RLX-DAY-001</h6>
                        <p class="card-text">
                            <strong>Order #:</strong> #293365<br>
                            <strong>Customer:</strong> Amira Hassan<br>
                            <strong>Condition:</strong> <span class="badge bg-info">Good</span>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-success">Accept</button>
                                <button class="btn btn-danger">Reject</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card grid-item">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/300x200/3498db/ffffff?text=OMG" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-danger">Wrong Size</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">SKU: OMG-SEA-001</h6>
                        <p class="card-text">
                            <strong>Order #:</strong> #293364<br>
                            <strong>Customer:</strong> Khaled Omar<br>
                            <strong>Condition:</strong> <span class="badge bg-success">Excellent</span>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-success">Accept</button>
                                <button class="btn btn-danger">Reject</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card grid-item">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/300x200/9b59b6/ffffff?text=TAG" class="card-img-top" alt="Product">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="badge bg-info">Changed Mind</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">SKU: TAG-CAR-001</h6>
                        <p class="card-text">
                            <strong>Order #:</strong> #293363<br>
                            <strong>Customer:</strong> Nadia Farid<br>
                            <strong>Type:</strong> <span class="badge bg-info">Related</span>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-success">Accept</button>
                                <button class="btn btn-danger">Reject</button>
                            </div>
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
        <span class="text-muted">Showing 1-6 of 6 items</span>
        <br>
        <strong>Total Return Value: EGP 42,500</strong>
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

    // Accept/Reject Returns
    document.querySelectorAll('.btn-success, .btn-danger').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.classList.contains('btn-success') ? 'accept' : 'reject';
            if (confirm(`Are you sure you want to ${action} this return?`)) {
                alert(`Return has been ${action}ed successfully!`);
                // Here you would make an AJAX call to update the return status
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
            alert('Please select at least one return');
            return;
        }

        alert(`${action} action will be performed on ${selectedOrders.length} selected returns`);
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

<!-- Modals for each return -->
@foreach($returns as $return)
<!-- Approve Return Modal -->
<div class="modal fade" id="approveReturnModal{{ $return->id }}" tabindex="-1" aria-labelledby="approveReturnModalLabel{{ $return->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveReturnModalLabel{{ $return->id }}">Approve Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('warehouse.approve-return', $return->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p>Are you sure you want to approve this return?</p>
                    <div class="alert alert-info">
                        <strong>Order #:</strong> {{ $return->order_number }}<br>
                        <strong>Customer:</strong> {{ $return->customer ? $return->customer->name : 'N/A' }}<br>
                        <strong>Value:</strong> EGP {{ number_format($return->total, 2) }}
                    </div>
                    <div class="mb-3">
                        <label for="approval_notes{{ $return->id }}" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="approval_notes{{ $return->id }}" name="approval_notes" rows="3" placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Return Modal -->
<div class="modal fade" id="rejectReturnModal{{ $return->id }}" tabindex="-1" aria-labelledby="rejectReturnModalLabel{{ $return->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectReturnModalLabel{{ $return->id }}">Reject Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('warehouse.reject-return', $return->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p>Are you sure you want to reject this return?</p>
                    <div class="alert alert-warning">
                        <strong>Order #:</strong> {{ $return->order_number }}<br>
                        <strong>Customer:</strong> {{ $return->customer ? $return->customer->name : 'N/A' }}<br>
                        <strong>Value:</strong> EGP {{ number_format($return->total, 2) }}
                    </div>
                    <div class="mb-3">
                        <label for="rejection_reason{{ $return->id }}" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason{{ $return->id }}" name="rejection_reason" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Return Details Modal -->
<div class="modal fade" id="returnDetailsModal{{ $return->id }}" tabindex="-1" aria-labelledby="returnDetailsModalLabel{{ $return->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnDetailsModalLabel{{ $return->id }}">Return Details - Order #{{ $return->order_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Order Number:</strong></td>
                                <td>{{ $return->order_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td><span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $return->status)) }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Total Value:</strong></td>
                                <td><strong>EGP {{ number_format($return->total, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Return Date:</strong></td>
                                <td>{{ $return->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Days Waiting:</strong></td>
                                <td><span class="badge {{ $return->created_at->diffInDays(now()) > 3 ? 'bg-danger' : 'bg-warning' }}">{{ $return->created_at->diffInDays(now()) }} days</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $return->customer ? $return->customer->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $return->customer ? $return->customer->phone : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Address:</strong></td>
                                <td>{{ $return->address ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($return->notes)
                <div class="mt-3">
                    <h6>Return Notes</h6>
                    <div class="alert alert-light">
                        {{ $return->notes }}
                    </div>
                </div>
                @endif

                @if($return->products && $return->products->count() > 0)
                <div class="mt-3">
                    <h6>Products in Return</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($return->products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->pivot->quantity ?? 1 }}</td>
                                    <td>EGP {{ number_format($product->price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveReturnModal{{ $return->id }}" data-bs-dismiss="modal">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectReturnModal{{ $return->id }}" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Reject
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
