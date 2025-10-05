@extends('layouts.main')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Exit Permission Details</h2>
                    <p class="text-muted mb-0">Order #{{ $order->order_number }}</p>
                </div>
                <div>
                    <a href="{{ route('warehouse.exit-permission') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Exit Permissions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Information -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Order Number:</strong></td>
                                    <td>{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer:</strong></td>
                                    <td>{{ $order->customer->name ?? 'Unknown Customer' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $order->customer->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $order->address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td><strong class="text-success">EGP {{ number_format($order->total, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @php
                                        $statusText = '';
                                        $statusClass = '';
                                        switch($order->status) {
                                        case '17':
                                        $statusText = 'Requested';
                                        $statusClass = 'warning';
                                        break;
                                        case '18':
                                        $statusText = 'Approved';
                                        $statusClass = 'info';
                                        break;
                                        case '19':
                                        $statusText = 'Rejected';
                                        $statusClass = 'danger';
                                        break;
                                        case '20':
                                        $statusText = 'Shipped';
                                        $statusClass = 'success';
                                        break;
                                        default:
                                        $statusText = 'Unknown';
                                        $statusClass = 'secondary';
                                        }
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Delivery Agent:</strong></td>
                                    <td>{{ $order->deliveryAgent->name ?? 'Not Assigned' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Expected Delivery:</strong></td>
                                    <td>{{ $order->expected_delivery_date ? \Carbon\Carbon::parse($order->expected_delivery_date)->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $order->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Products</h5>
                </div>
                <div class="card-body">
                    @if($order->products && $order->products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sku ?? 'N/A' }}</td>
                                    <td>{{ $product->pivot->quantity ?? $product->quantity ?? 1 }}</td>
                                    <td>{{ $product->stock_quantity ?? 'N/A' }}</td>
                                    <td>
                                        @if(($product->stock_quantity ?? 0) > 0)
                                        <span class="badge bg-success">In Stock</span>
                                        @else
                                        <span class="badge bg-danger">Out of Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">No products found for this order.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Notes -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Notes</h5>
                </div>
                <div class="card-body">
                    @if($order->notes)
                    <div class="notes-content">
                        {!! nl2br(e($order->notes)) !!}
                    </div>
                    @else
                    <p class="text-muted">No notes available.</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if($order->status == '17')
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="approveOrder({{ $order->id }})">
                            <i class="fas fa-check me-2"></i>Approve
                        </button>
                        <button class="btn btn-danger" onclick="rejectOrder({{ $order->id }})">
                            <i class="fas fa-times me-2"></i>Reject
                        </button>
                    </div>
                </div>
            </div>
            @elseif($order->status == '18')
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="shipOrder({{ $order->id }})">
                            <i class="fas fa-shipping-fast me-2"></i>Ship
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    function approveOrder(orderId) {
        if (confirm('Are you sure you want to approve this exit permission?')) {
            processOrder(orderId, 'approve');
        }
    }

    function rejectOrder(orderId) {
        const reason = prompt('Please provide a reason for rejection:');
        if (reason === null) return;

        if (reason.trim() === '') {
            alert('Please provide a reason for rejection.');
            return;
        }

        processOrder(orderId, 'reject', reason);
    }

    function shipOrder(orderId) {
        if (confirm('Are you sure you want to ship this exit permission?')) {
            processOrder(orderId, 'ship');
        }
    }

    function processOrder(orderId, action, notes = '') {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('action', action);
        if (notes) {
            formData.append('notes', notes);
        }

        fetch(`/warehouse/process-exit-permission/${orderId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Action completed successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to process action');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing the request');
            });
    }
</script>

<style>
    .notes-content {
        max-height: 300px;
        overflow-y: auto;
        white-space: pre-wrap;
    }
</style>
@endsection
