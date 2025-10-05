@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="dashboard-header mb-1">
                        <i class="fas fa-clipboard-list text-primary me-2"></i>
                        My Delivery Orders
                    </h2>
                    <p class="text-muted mb-0">Manage your assigned delivery orders</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-1"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['total_orders'] ?? 0 }}</h4>
                            <small>Total Orders</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-list"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['pending_orders'] ?? 0 }}</h4>
                            <small>Pending</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['in_transit_orders'] ?? 0 }}</h4>
                            <small>In Transit</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['delivered_orders'] ?? 0 }}</h4>
                            <small>Delivered</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['exit_requests'] ?? 0 }}</h4>
                            <small>Exit Requests</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['returned_orders'] ?? 0 }}</h4>
                            <small>Returned</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-undo"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('driver.my-orders') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                        value="{{ request('search') }}" placeholder="Order #, Customer name, Phone...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="{{ config('constants.OUT_FOR_DELIVERY') }}" {{ request('status') == config('constants.OUT_FOR_DELIVERY') ? 'selected' : '' }}>Pending Delivery</option>
                        <option value="{{ config('constants.DELIVERED') }}" {{ request('status') == config('constants.DELIVERED') ? 'selected' : '' }}>Delivered</option>
                        <option value="{{ config('constants.EXIT_REQUESTED') }}" {{ request('status') == config('constants.EXIT_REQUESTED') ? 'selected' : '' }}>Exit Requested</option>
                        <option value="{{ config('constants.EXIT_APPROVED') }}" {{ request('status') == config('constants.EXIT_APPROVED') ? 'selected' : '' }}>Exit Approved</option>
                        <option value="{{ config('constants.EXIT_REJECTED') }}" {{ request('status') == config('constants.EXIT_REJECTED') ? 'selected' : '' }}>Exit Rejected</option>
                        <option value="{{ config('constants.EXIT_SHIPPED') }}" {{ request('status') == config('constants.EXIT_SHIPPED') ? 'selected' : '' }}>Exit Shipped</option>
                        <option value="{{ config('constants.RETURNED') }}" {{ request('status') == config('constants.RETURNED') ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="order_type" class="form-label">Order Type</label>
                    <select class="form-select" id="order_type" name="order_type">
                        <option value="">All Types</option>
                        <option value="delivery" {{ request('order_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                        <option value="exit" {{ request('order_type') == 'exit' ? 'selected' : '' }}>Exit Request</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date"
                        value="{{ request('date') }}">
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

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">My Assigned Orders</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Type</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Governorate</th>
                            <th>Address</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Assigned Date</th>
                            <th>Status</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr class="table-{{ $order->status == config('constants.DELIVERED') ? 'success' : ($order->status == config('constants.EXIT_SHIPPED') ? 'info' : 'warning') }}" data-order-id="{{ $order->id }}" data-order-type="{{ in_array($order->status, [config('constants.EXIT_REQUESTED'), config('constants.EXIT_APPROVED'), config('constants.EXIT_REJECTED'), config('constants.EXIT_SHIPPED')]) ? 'exit' : 'delivery' }}">
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>
                                @if(in_array($order->status, [config('constants.EXIT_REQUESTED'), config('constants.EXIT_APPROVED'), config('constants.EXIT_REJECTED'), config('constants.EXIT_SHIPPED')]))
                                <span class="badge bg-secondary">Exit Request</span>
                                @else
                                <span class="badge bg-primary">Delivery</span>
                                @endif
                            </td>
                            <td>{{ $order->customer->name ?? 'Unknown' }}</td>
                            <td>{{ $order->customer->phone_numbers ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $order->governorate ?? 'N/A' }}</span></td>
                            <td>{{ Str::limit($order->address ?? 'N/A', 40) }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $order->products->count() }} items</span>
                            </td>
                            <td><strong>EGP {{ number_format($order->total, 2) }}</strong></td>
                            <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                @if($order->status == config('constants.DELIVERED'))
                                <span class="badge bg-success">Delivered</span>
                                @elseif($order->status == config('constants.EXIT_REQUESTED'))
                                <span class="badge bg-warning">Exit Requested</span>
                                @elseif($order->status == config('constants.EXIT_APPROVED'))
                                <span class="badge bg-info">Exit Approved</span>
                                @elseif($order->status == config('constants.EXIT_REJECTED'))
                                <span class="badge bg-danger">Exit Rejected</span>
                                @elseif($order->status == config('constants.EXIT_SHIPPED'))
                                <span class="badge bg-success">Exit Shipped</span>
                                @elseif($order->status == config('constants.RETURNED'))
                                <span class="badge bg-danger">Returned</span>
                                @else
                                <span class="badge bg-warning">Pending Delivery</span>
                                @endif
                            </td>
                            <td>
                                @if($order->status == config('constants.DELIVERED'))
                                <span class="text-success">
                                    <i class="fas fa-check-circle"></i> Completed
                                </span>
                                @elseif($order->status == config('constants.EXIT_SHIPPED'))
                                {{-- Exit Shipped - show Delivered and Returned buttons --}}
                                <div class="btn-group btn-group-sm d-flex gap-1">
                                    <button class="btn btn-primary" onclick="markExitAsDelivered({{ $order->id }})" title="Mark as Delivered">
                                        <i class="fas fa-check-circle"></i> Delivered
                                    </button>
                                    <button class="btn btn-warning" onclick="markExitAsReturned({{ $order->id }})" title="Mark as Returned">
                                        <i class="fas fa-undo"></i> Returned
                                    </button>
                                </div>
                                @elseif($order->status == config('constants.EXIT_REJECTED'))
                                <span class="text-danger">
                                    <i class="fas fa-times-circle"></i> Rejected
                                </span>
                                @elseif($order->status == config('constants.RETURNED'))
                                <span class="text-danger">
                                    <i class="fas fa-undo"></i> Returned
                                </span>
                                @elseif($order->status == config('constants.EXIT_REQUESTED'))
                                {{-- Exit Request - show Accept and Reject buttons --}}
                                <div class="btn-group btn-group-sm d-flex gap-1">
                                    <button class="btn btn-success" onclick="acceptExitRequest({{ $order->id }})" title="Accept Exit Request">
                                        <i class="fas fa-check"></i> Accept
                                    </button>
                                    <button class="btn btn-danger" onclick="rejectExitRequest({{ $order->id }})" title="Reject Exit Request">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                                @elseif($order->status == config('constants.EXIT_APPROVED'))
                                {{-- Exit Approved - show Ship button --}}
                                <div class="btn-group btn-group-sm d-flex gap-1">
                                    <button class="btn btn-primary" onclick="shipExitRequest({{ $order->id }})" title="Ship Exit Request">
                                        <i class="fas fa-shipping-fast"></i> Ship
                                    </button>
                                </div>
                                @elseif($order->status == config('constants.OUT_FOR_DELIVERY') && strpos($order->notes ?? '', 'Driver accepted order at:') !== false)
                                {{-- Order has been accepted by driver - show Mark as Delivered and Mark as Returned --}}
                                <div class="btn-group btn-group-sm d-flex gap-1">
                                    <button class="btn btn-primary" onclick="markAsDelivered({{ $order->id }})" title="Mark as Delivered">
                                        <i class="fas fa-check-circle"></i> Delivered
                                    </button>
                                    <button class="btn btn-warning" onclick="markAsReturned({{ $order->id }})" title="Mark as Returned">
                                        <i class="fas fa-undo"></i> Returned
                                    </button>
                                </div>
                                @elseif($order->status == config('constants.OUT_FOR_DELIVERY'))
                                {{-- Order not yet acted upon by driver - show Accept and Reject buttons --}}
                                <div class="btn-group btn-group-sm d-flex gap-1">
                                    <button class="btn btn-success" onclick="acceptOrder({{ $order->id }})" title="Accept Order">
                                        <i class="fas fa-check"></i> Accept
                                    </button>
                                    <button class="btn btn-danger" onclick="rejectOrder({{ $order->id }})" title="Reject Order">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                                @else
                                {{-- Order in other status - no actions available --}}
                                <span class="text-muted">
                                    <i class="fas fa-info-circle"></i> No actions available
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No orders found</h5>
                                <p class="text-muted">You don't have any assigned orders yet</p>
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

<!-- Reject Order Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Rejection *</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required
                            placeholder="Please explain why you're rejecting this order..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This order will be returned to the warehouse for reassignment.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mark as Delivered Modal -->
<div class="modal fade" id="deliveredModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Mark Order as Delivered</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deliveredForm" method="POST">
                @csrf
                <input type="hidden" name="order_type" id="delivered_order_type" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="delivery_notes" class="form-label">Delivery Notes (Optional)</label>
                        <textarea class="form-control" id="delivery_notes" name="notes" rows="3"
                            placeholder="Add any notes about the delivery..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Please confirm that the order was successfully delivered to the customer.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>
                        Confirm Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mark as Returned Modal -->
<div class="modal fade" id="returnedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Mark Order as Returned</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="returnedForm" method="POST">
                @csrf
                <input type="hidden" name="order_type" id="returned_order_type" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="return_reason" class="form-label">Return Reason *</label>
                        <textarea class="form-control" id="return_reason" name="reason" rows="3" required
                            placeholder="Please explain why the order is being returned..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="return_notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="return_notes" name="notes" rows="2"
                            placeholder="Add any additional notes about the return..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This order will be marked as returned and returned to the warehouse for processing.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo me-1"></i>
                        Confirm Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Exit Request Modal -->
<div class="modal fade" id="rejectExitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Exit Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectExitForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exit_reason" class="form-label">Reason for Rejection *</label>
                        <textarea class="form-control" id="exit_reason" name="reason" rows="3" required
                            placeholder="Please explain why you're rejecting this exit request..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This exit request will be marked as rejected and returned to the warehouse.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function acceptOrder(orderId) {
        if (confirm('Accept this delivery order?')) {
            fetch(`/driver/accept-order/${orderId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Failed to accept order. Please try again.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }

    function rejectOrder(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        const form = document.getElementById('rejectForm');
        form.action = `/driver/reject-order/${orderId}`;
        modal.show();
    }

    function markAsDelivered(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('deliveredModal'));
        const form = document.getElementById('deliveredForm');
        form.action = `/driver/mark-as-delivered/${orderId}`;
        modal.show();
    }

    function markAsReturned(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('returnedModal'));
        const form = document.getElementById('returnedForm');
        form.action = `/driver/mark-as-returned/${orderId}`;
        modal.show();
    }

    function acceptExitRequest(orderId) {
        if (confirm('Accept this exit request?')) {
            fetch(`/driver/accept-exit-request/${orderId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Failed to accept exit request. Please try again.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }

    function rejectExitRequest(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('rejectExitModal'));
        const form = document.getElementById('rejectExitForm');
        form.action = `/driver/reject-exit-request/${orderId}`;
        modal.show();
    }

    function shipExitRequest(orderId) {
        if (confirm('Mark this exit request as shipped?')) {
            fetch(`/driver/ship-exit-request/${orderId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Failed to ship exit request. Please try again.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }

    function markExitAsDelivered(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('deliveredModal'));
        const form = document.getElementById('deliveredForm');
        form.action = `/driver/mark-as-delivered/${orderId}`;
        modal.show();
    }

    function markExitAsReturned(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('returnedModal'));
        const form = document.getElementById('returnedForm');
        form.action = `/driver/mark-as-returned/${orderId}`;
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
</style>
@endsection
