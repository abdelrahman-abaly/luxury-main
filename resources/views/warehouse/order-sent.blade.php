@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="dashboard-header mb-1">
                        <i class="fas fa-truck text-success me-2"></i>
                        Orders Sent to Drivers
                    </h2>
                    <p class="text-muted mb-0">Track orders assigned to delivery drivers</p>
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
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ number_format($stats['assigned_orders'] ?? 0) }}</h4>
                            <small>Assigned Orders</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ number_format($stats['active_drivers'] ?? 0) }}</h4>
                            <small>Active Drivers</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-user-tie"></i>
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
                            <h4 class="mb-0">{{ number_format($stats['in_transit_orders'] ?? 0) }}</h4>
                            <small>In Transit</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-shipping-fast"></i>
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
            <form method="GET" action="{{ route('warehouse.order-sent') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Order #, Customer, Driver...">
                </div>
                <div class="col-md-2">
                    <label for="driver" class="form-label">Driver</label>
                    <select class="form-select" id="driver" name="driver">
                        <option value="">All Drivers</option>
                        @foreach($drivers as $driver)
                        <option value="{{ $driver->user_id }}" {{ request('driver') == $driver->user_id ? 'selected' : '' }}>{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="{{ config('constants.OUT_FOR_DELIVERY') }}" {{ request('status') == config('constants.OUT_FOR_DELIVERY') ? 'selected' : '' }}>In Transit</option>
                        <option value="{{ config('constants.EXIT_REQUESTED') }}" {{ request('status') == config('constants.EXIT_REQUESTED') ? 'selected' : '' }}>Exit Requested</option>
                        <option value="{{ config('constants.EXIT_APPROVED') }}" {{ request('status') == config('constants.EXIT_APPROVED') ? 'selected' : '' }}>Exit Approved</option>
                        <option value="{{ config('constants.EXIT_REJECTED') }}" {{ request('status') == config('constants.EXIT_REJECTED') ? 'selected' : '' }}>Exit Rejected</option>
                        <option value="{{ config('constants.EXIT_SHIPPED') }}" {{ request('status') == config('constants.EXIT_SHIPPED') ? 'selected' : '' }}>Exit Shipped</option>
                        <option value="{{ config('constants.DELIVERED') }}" {{ request('status') == config('constants.DELIVERED') ? 'selected' : '' }}>Delivered</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
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
            <h5 class="mb-0">Orders Assigned to Drivers</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Driver</th>
                            <th>Driver Phone</th>
                            <th>Governorate</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Assigned Date</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        @php
                        $rowClass = 'warning'; // default
                        if($order->status == config('constants.DELIVERED')) {
                        $rowClass = 'success';
                        } elseif($order->status == config('constants.EXIT_SHIPPED')) {
                        $rowClass = 'info';
                        } elseif($order->status == config('constants.EXIT_APPROVED')) {
                        $rowClass = 'primary';
                        } elseif($order->status == config('constants.EXIT_REJECTED')) {
                        $rowClass = 'danger';
                        }
                        @endphp
                        <tr class="table-{{ $rowClass }}">
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
                            <td>
                                @if($order->deliveryAgent)
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <strong>{{ $order->deliveryAgent->name }}</strong>
                                </div>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $order->deliveryAgent->phone ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $order->governorate ?? 'N/A' }}</span></td>
                            <td>
                                <span class="badge bg-primary">{{ $order->products->count() }} items</span>
                            </td>
                            <td><strong>EGP {{ number_format($order->total, 2) }}</strong></td>
                            <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                @if($order->status == config('constants.DELIVERED'))
                                <span class="badge bg-success">Delivered</span>
                                @elseif($order->status == config('constants.EXIT_SHIPPED'))
                                <span class="badge bg-info">Exit Shipped</span>
                                @elseif($order->status == config('constants.EXIT_APPROVED'))
                                <span class="badge bg-primary">Exit Approved</span>
                                @elseif($order->status == config('constants.EXIT_REJECTED'))
                                <span class="badge bg-danger">Exit Rejected</span>
                                @elseif($order->status == config('constants.EXIT_REQUESTED'))
                                <span class="badge bg-warning">Exit Requested</span>
                                @else
                                <span class="badge bg-warning">In Transit</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-info" onclick="viewOrder('{{ $order->order_number }}')" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($order->status != config('constants.DELIVERED'))
                                    <button class="btn btn-outline-danger" onclick="openReassignModal({{ $order->id }})" title="Reassign">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    @endif
                                    @if($order->status == config('constants.EXIT_REJECTED'))
                                    <button class="btn btn-outline-warning" onclick="requestExitAgain({{ $order->id }})" title="Request Exit Again">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No orders found</h5>
                                <p class="text-muted">No orders have been assigned to drivers yet</p>
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

    <!-- Driver Summary Card -->
    @if($driverSummaries->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Active Drivers Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($driverSummaries as $driver)
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">{{ $driver->name }}</h6>
                                    <span class="badge bg-success">Active</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Assigned Orders:</span>
                                    <strong>{{ $driver->assigned_count ?? 0 }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">In Transit:</span>
                                    <strong>{{ $driver->in_transit_count ?? 0 }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Total Value:</span>
                                    <strong>EGP {{ number_format($driver->deliveryOrders->sum('total') ?? 0, 0) }}</strong>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Reassign Modal -->
<div class="modal fade" id="reassignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Reassign Order to Driver</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reassignForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reassign_driver_id" class="form-label">Select New Driver *</label>
                        <select class="form-select" id="reassign_driver_id" name="driver_id" required>
                            <option value="">Choose a driver...</option>
                            @foreach($drivers as $driver)
                            <option value="{{ $driver->user_id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This order will be reassigned to the selected driver.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-exchange-alt me-1"></i>
                        Reassign Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function viewOrder(orderNumber) {
        window.location.href = `/warehouse/waiting-send?search=${orderNumber}`;
    }

    function openReassignModal(orderId) {
        const form = document.getElementById('reassignForm');
        form.action = `/warehouse/reassign-driver/${orderId}`;

        const modal = new bootstrap.Modal(document.getElementById('reassignModal'));
        modal.show();
    }

    function requestExitAgain(orderId) {
        if (confirm('Are you sure you want to request exit permission again for this order?')) {
            // You can implement this functionality later
            alert('Exit permission request functionality will be implemented soon.');
        }
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

    .btn-group .btn {
        border-radius: 0.375rem;
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

    .border {
        border: 1px solid #dee2e6 !important;
    }

    .rounded {
        border-radius: 0.375rem !important;
    }
</style>
@endsection
