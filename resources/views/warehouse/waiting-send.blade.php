@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="dashboard-header mb-1">
                        <i class="fas fa-truck-loading text-warning me-2"></i>
                        Waiting to Send Orders
                    </h2>
                    <p class="text-muted mb-0">Orders ready for delivery - Assign to drivers</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-warning" onclick="openBulkAssignModal()">
                        <i class="fas fa-users me-1"></i>
                        Bulk Assign
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ number_format($stats['waiting_orders'] ?? 0) }}</h4>
                            <small>Waiting Orders</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-clock"></i>
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
                            <h4 class="mb-0">{{ number_format($stats['urgent_orders'] ?? 0) }}</h4>
                            <small>Urgent Orders</small>
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
                            <h4 class="mb-0">{{ number_format($stats['available_drivers'] ?? 0) }}</h4>
                            <small>Available Drivers</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-user-tie"></i>
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
                            <h4 class="mb-0">EGP {{ number_format($stats['total_value'] ?? 0, 0) }}</h4>
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
            <form method="GET" action="{{ route('warehouse.waiting-send') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Order #, Customer name...">
                </div>
                <div class="col-md-2">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All Priorities</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="governorate" class="form-label">Governorate</label>
                    <select class="form-select" id="governorate" name="governorate">
                        <option value="">All Governorates</option>
                        @foreach($governorates as $gov)
                        <option value="{{ $gov }}" {{ request('governorate') === $gov ? 'selected' : '' }}>{{ $gov }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
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
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Orders Waiting for Driver Assignment</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                        <i class="fas fa-check-square me-1"></i>
                        Select All
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
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Governorate</th>
                            <th>Address</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th width="250">Assign Driver</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr class="table-{{ $order->notes && stripos($order->notes, 'urgent') !== false ? 'danger' : 'warning' }}">
                            <td>
                                <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}">
                            </td>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>
                                @if($order->customer)
                                {{ $order->customer->name }}
                                @else
                                <span class="text-muted">Unknown</span>
                                @endif
                            </td>
                            <td>
                                @if($order->customer)
                                {{ $order->customer->phone_numbers }}
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td><span class="badge bg-info">{{ $order->governorate ?? 'N/A' }}</span></td>
                            <td>{{ Str::limit($order->address ?? 'N/A', 30) }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $order->products->count() }} items</span>
                            </td>
                            <td><strong>EGP {{ number_format($order->total, 2) }}</strong></td>
                            <td>
                                <span class="badge bg-warning">Waiting</span>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route('warehouse.assign-driver', $order->id) }}" class="d-flex gap-1">
                                    @csrf
                                    <select class="form-select form-select-sm" name="driver_id" required style="width: 150px;">
                                        <option value="">Select Driver</option>
                                        @foreach($drivers as $driver)
                                        <option value="{{ $driver->user_id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No orders waiting for assignment</h5>
                                <p class="text-muted">All orders have been assigned to drivers</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
                </div>
                <nav aria-label="Page navigation">
                    {{ $orders->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Bulk Assign Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Bulk Assign Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkAssignForm" method="POST" action="{{ route('warehouse.bulk-assign-driver') }}">
                @csrf
                <input type="hidden" name="order_ids[]" id="selectedOrderIds">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulk_driver_id" class="form-label">Select Driver *</label>
                        <select class="form-select" id="bulk_driver_id" name="driver_id" required>
                            <option value="">Choose a driver...</option>
                            @foreach($drivers as $driver)
                            <option value="{{ $driver->user_id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="selectedCount">0</span> orders selected for assignment
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-check me-1"></i>
                        Assign Orders
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function selectAll() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        selectAllCheckbox.checked = !selectAllCheckbox.checked;
        const checkboxes = document.querySelectorAll('.order-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.order-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    function openBulkAssignModal() {
        const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
        if (selectedCheckboxes.length === 0) {
            alert('Please select at least one order!');
            return;
        }

        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        // Clear previous hidden inputs
        const form = document.getElementById('bulkAssignForm');
        const oldInputs = form.querySelectorAll('input[name="order_ids[]"]');
        oldInputs.forEach(input => input.remove());

        // Add new hidden inputs for each selected order
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'order_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        document.getElementById('selectedCount').textContent = selectedIds.length;

        const modal = new bootstrap.Modal(document.getElementById('bulkAssignModal'));
        modal.show();
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

    .badge {
        font-size: 0.75em;
    }

    .card-footer {
        background-color: #f8f9fa;
        padding: 0.75rem 1.25rem;
    }

    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
</style>
@endsection
