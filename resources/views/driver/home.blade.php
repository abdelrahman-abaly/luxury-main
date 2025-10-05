@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 dashboard-header">Driver Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }} - Delivery Driver</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-primary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Assigned</h5>
                    <h3 class="mb-1">{{ number_format($stats['assigned_orders'] ?? 0) }}</h3>
                    <p class="mb-0">All Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Pending Delivery</h5>
                    <h3 class="mb-1">{{ number_format($stats['pending_orders'] ?? 0) }}</h3>
                    <p class="mb-0">Need Delivery</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Delivered</h5>
                    <h3 class="mb-1">{{ number_format($stats['delivered_orders'] ?? 0) }}</h3>
                    <p class="mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Today's Orders</h5>
                    <h3 class="mb-1">{{ number_format($stats['today_orders'] ?? 0) }}</h3>
                    <p class="mb-0">New Today</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i>Delivery Performance
                </h5>
                <small class="text-muted">Last 7 days delivery tracking</small>
            </div>
        </div>
        <div class="card-body p-4">
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="deliveryChart"></canvas>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="fas fa-box text-success fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted small">Total Delivered (7 days)</h6>
                            <h4 class="mb-0 fw-bold">{{ array_sum($chartData['data'] ?? [0]) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="fas fa-dollar-sign text-primary fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted small">Total Value</h6>
                            <h4 class="mb-0 fw-bold">EGP {{ number_format(array_sum($chartData['values'] ?? [0]), 0) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                <i class="fas fa-chart-bar text-warning fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted small">Daily Average</h6>
                            <h4 class="mb-0 fw-bold">{{ count($chartData['data'] ?? [1]) > 0 ? round(array_sum($chartData['data'] ?? [0]) / count($chartData['data']), 1) : 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Orders -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-day me-2"></i>
                    Today's Orders
                </h5>
                <a href="{{ route('driver.my-orders') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list me-1"></i>
                    View All Orders
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayOrders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->customer->name ?? 'Unknown' }}</td>
                            <td>{{ $order->customer->phone_numbers ?? 'N/A' }}</td>
                            <td>{{ Str::limit($order->address ?? 'N/A', 30) }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $order->products->count() }} items</span>
                            </td>
                            <td><strong>EGP {{ number_format($order->total, 2) }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $order->status == config('constants.DELIVERED') ? 'success' : 'warning' }}">
                                    {{ $order->status == config('constants.DELIVERED') ? 'Delivered' : 'Pending' }}
                                </span>
                            </td>
                            <td>
                                @if($order->status == config('constants.DELIVERED'))
                                <span class="text-success">
                                    <i class="fas fa-check-circle"></i> Completed
                                </span>
                                @elseif($order->status == config('constants.OUT_FOR_DELIVERY') && strpos($order->notes ?? '', 'Driver accepted order at:') !== false)
                                {{-- Order has been accepted by driver - show Mark as Delivered and Mark as Returned --}}
                                <div class="btn-group btn-group-sm d-flex gap-1">
                                    <button class="btn btn-primary" onclick="markAsDelivered({{ $order->id }})" title="Mark as Delivered">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                    <button class="btn btn-warning" onclick="markAsReturned({{ $order->id }})" title="Mark as Returned">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button class="btn btn-info" onclick="viewOrder({{ $order->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @elseif($order->status == config('constants.OUT_FOR_DELIVERY'))
                                {{-- Order not yet acted upon by driver - show Accept and Reject buttons --}}
                                <div class="btn-group btn-group-sm d-flex gap-1">
                                    <button class="btn btn-success" onclick="acceptOrder({{ $order->id }})" title="Accept Order">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="rejectOrder({{ $order->id }})" title="Reject Order">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button class="btn btn-info" onclick="viewOrder({{ $order->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @else
                                {{-- Order in other status - show only view button --}}
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info" onclick="viewOrder({{ $order->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No orders for today</h5>
                                <p class="text-muted">New orders will appear here</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Delivery Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any notes about the delivery..."></textarea>
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

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // const chartData = {
    //     !!json_encode($chartData ?? ['labels' => [], 'data' => [], 'values' => []]) !!
    // };
    const chartData = {!! json_encode($chartData ?? ['labels' => [], 'data' => [], 'values' => []]) !!};
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('deliveryChart');

        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartData.labels || [],
                    datasets: [{
                        label: 'Deliveries',
                        data: chartData.data || [],
                        borderColor: 'rgb(25, 135, 84)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    });

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
        const reason = prompt('Please enter reason for rejection:');
        if (reason && reason.trim() !== '') {
            const formData = new FormData();
            formData.append('reason', reason);
            formData.append('_token', '{{ csrf_token() }}');

            fetch(`/driver/reject-order/${orderId}`, {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Failed to reject order. Please try again.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }

    function markAsDelivered(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('deliveredModal'));
        const form = document.getElementById('deliveredForm');
        form.action = `/driver/mark-as-delivered/${orderId}`;
        modal.show();
    }

    function markAsReturned(orderId) {
        const reason = prompt('Please enter return reason:');
        if (reason && reason.trim() !== '') {
            const formData = new FormData();
            formData.append('reason', reason);
            formData.append('_token', '{{ csrf_token() }}');

            fetch(`/driver/mark-as-returned/${orderId}`, {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Failed to mark as returned. Please try again.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }

    function viewOrder(orderId) {
        window.location.href = `/driver/my-orders?search=${orderId}`;
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

    .shadow-sm {
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1) !important;
    }

    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }

    .rounded-circle {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .table th {
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .btn-group .btn {
        border-radius: 0.375rem;
    }
</style>
@endsection
