@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="dashboard-header mb-1">
                        <i class="fas fa-utensils text-success me-2"></i>
                        Feeding Requests Management
                    </h2>
                    <p class="text-muted mb-0">Manage feeding requests and meal orders for warehouse staff</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="toggleView()">
                        <i class="fas fa-th-large me-1"></i>
                        <span id="viewToggleText">Grid View</span>
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newFeedingRequestModal">
                        <i class="fas fa-plus me-1"></i>
                        New Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['total_requests'] ?? 0 }}</h4>
                            <small>Total Requests</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-utensils"></i>
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
                            <h4 class="mb-0">{{ $stats['urgent_requests'] ?? 0 }}</h4>
                            <small>Urgent Requests</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-exclamation-triangle"></i>
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
                            <h4 class="mb-0">{{ $stats['pending_requests'] ?? 0 }}</h4>
                            <small>Pending Requests</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-clock"></i>
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
                            <h4 class="mb-0">{{ $stats['completed_requests'] ?? 0 }}</h4>
                            <small>Completed Requests</small>
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
                            <h4 class="mb-0">EGP {{ number_format($stats['total_value'] ?? 0, 2) }}</h4>
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
            <form method="GET" action="{{ route('warehouse.feeding-requests') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                        value="{{ request('search') }}" placeholder="Search by order number, customer name, or phone">
                </div>
                <div class="col-md-2">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All Priorities</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="13" {{ request('status') == '13' ? 'selected' : '' }}>Requested</option>
                        <option value="14" {{ request('status') == '14' ? 'selected' : '' }}>Processing</option>
                        <option value="16" {{ request('status') == '16' ? 'selected' : '' }}>Completed</option>
                        <option value="15" {{ request('status') == '15' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from"
                        value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to"
                        value="{{ request('date_to') }}">
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
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Feeding Requests</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                        <i class="fas fa-check-square me-1"></i>
                        Select All
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="bulkAction()">
                        <i class="fas fa-check me-1"></i>
                        Bulk Action
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
                            <th>Products</th>
                            <th>Stock Impact</th>
                            <th>Priority</th>
                            <th>Request Date</th>
                            <th>Total Value</th>
                            <th>Status</th>
                            <th>Days Waiting</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedingRequests as $request)
                        <tr class="table-{{ $request->notes && stripos($request->notes, 'urgent') !== false ? 'danger' : 'success' }}">
                            <td>
                                <input type="checkbox" class="form-check-input feeding-checkbox" value="{{ $request->id }}">
                            </td>
                            <td><strong>{{ $request->order_number }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $request->customer->name ?? 'Unknown Customer' }}</strong><br>
                                    <small class="text-muted">{{ $request->customer->phone_numbers ?? 'No phone' }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    @foreach($request->products->take(2) as $product)
                                    <span class="badge bg-info me-1">{{ $product->name }}</span>
                                    @endforeach
                                    @if($request->products->count() > 2)
                                    <span class="badge bg-secondary">+{{ $request->products->count() - 2 }} more</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    @php
                                    $totalQuantity = $request->products->sum(function($product) {
                                    return $product->pivot->quantity ?? 0;
                                    });
                                    @endphp
                                    <span class="badge bg-success text-white">
                                        <i class="fas fa-plus"></i> +{{ $totalQuantity }}
                                    </span>
                                    <br>
                                    <small class="text-muted">units</small>
                                </div>
                            </td>
                            <td>
                                @php
                                $priority = 'Medium';
                                if ($request->notes && stripos($request->notes, 'urgent') !== false) $priority = 'Urgent';
                                elseif ($request->notes && stripos($request->notes, 'high') !== false) $priority = 'High';
                                elseif ($request->notes && stripos($request->notes, 'low') !== false) $priority = 'Low';
                                @endphp
                                <span class="badge bg-{{ $priority == 'Urgent' ? 'danger' : ($priority == 'High' ? 'warning' : ($priority == 'Low' ? 'info' : 'success')) }}">
                                    {{ $priority }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>EGP {{ number_format($request->total, 2) }}</td>
                            <td>
                                @php
                                $statusClass = 'warning';
                                $statusText = 'Pending';
                                if ($request->status == config('constants.FEEDING_REQUESTED')) {
                                $statusClass = 'warning';
                                $statusText = 'Requested';
                                } elseif ($request->status == config('constants.FEEDING_PROCESSING')) {
                                $statusClass = 'info';
                                $statusText = 'Processing';
                                } elseif ($request->status == config('constants.FEEDING_COMPLETED')) {
                                $statusClass = 'success';
                                $statusText = 'Completed';
                                } elseif ($request->status == config('constants.FEEDING_REJECTED')) {
                                $statusClass = 'danger';
                                $statusText = 'Rejected';
                                }
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $request->created_at->diffInDays(now()) > 3 ? 'danger' : ($request->created_at->diffInDays(now()) > 1 ? 'warning' : 'success') }}">
                                    {{ $request->created_at->diffInDays(now()) }} days
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if($request->status == config('constants.FEEDING_REQUESTED'))
                                    <button class="btn btn-sm btn-outline-success"
                                        onclick="approveRequest({{ $request->id ?? 0 }})" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick="holdRequest({{ $request->id ?? 0 }})" title="Hold">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="rejectRequest({{ $request->id ?? 0 }})" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @elseif($request->status == config('constants.FEEDING_PROCESSING'))
                                    <button class="btn btn-sm btn-outline-success"
                                        onclick="completeRequest({{ $request->id ?? 0 }})" title="Complete">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick="holdRequest({{ $request->id ?? 0 }})" title="Hold">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                    @else
                                    <span class="text-muted">No actions available</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No feeding requests found</h5>
                                <p class="text-muted">All requests have been processed.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Grid View -->
    <div class="card d-none" id="gridView">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Feeding Requests - Grid View</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                        <i class="fas fa-check-square me-1"></i>
                        Select All
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="bulkAction()">
                        <i class="fas fa-tasks me-1"></i>
                        Bulk Action
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($feedingRequests as $request)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-{{ $request->notes && stripos($request->notes, 'urgent') !== false ? 'danger' : 'success' }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input feeding-checkbox" value="{{ $request->id }}">
                            </div>
                            <div class="text-end">
                                <strong class="text-primary">{{ $request->order_number }}</strong>
                                <br>
                                <small class="text-muted">{{ $request->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Customer Info -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Customer</h6>
                                <strong>{{ $request->customer->name ?? 'Unknown Customer' }}</strong>
                                <br>
                                <small class="text-muted">{{ $request->customer->phone_numbers ?? 'No phone' }}</small>
                            </div>

                            <!-- Products -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Products</h6>
                                @foreach($request->products->take(3) as $product)
                                <span class="badge bg-info me-1 mb-1">{{ $product->name }}</span>
                                @endforeach
                                @if($request->products->count() > 3)
                                <span class="badge bg-secondary">+{{ $request->products->count() - 3 }} more</span>
                                @endif
                            </div>

                            <!-- Stock Impact -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Stock Impact</h6>
                                @php
                                $totalQuantity = $request->products->sum(function($product) {
                                return $product->pivot->quantity ?? 0;
                                });
                                @endphp
                                <span class="badge bg-success text-white fs-6">
                                    <i class="fas fa-plus"></i> +{{ $totalQuantity }} units
                                </span>
                            </div>

                            <!-- Priority -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Priority</h6>
                                @php
                                $priority = 'Medium';
                                if ($request->notes && stripos($request->notes, 'urgent') !== false) $priority = 'Urgent';
                                elseif ($request->notes && stripos($request->notes, 'high') !== false) $priority = 'High';
                                elseif ($request->notes && stripos($request->notes, 'low') !== false) $priority = 'Low';
                                @endphp
                                <span class="badge bg-{{ $priority == 'Urgent' ? 'danger' : ($priority == 'High' ? 'warning' : ($priority == 'Low' ? 'info' : 'success')) }}">
                                    {{ $priority }}
                                </span>
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Status</h6>
                                @php
                                $statusConfig = config('constants');
                                $statusClass = 'secondary';
                                $statusText = 'Unknown';

                                if ($request->status == $statusConfig['FEEDING_REQUESTED']) {
                                $statusClass = 'warning';
                                $statusText = 'Requested';
                                } elseif ($request->status == $statusConfig['FEEDING_PROCESSING']) {
                                $statusClass = 'info';
                                $statusText = 'Processing';
                                } elseif ($request->status == $statusConfig['FEEDING_COMPLETED']) {
                                $statusClass = 'success';
                                $statusText = 'Completed';
                                } elseif ($request->status == $statusConfig['FEEDING_REJECTED']) {
                                $statusClass = 'danger';
                                $statusText = 'Rejected';
                                }
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                            </div>

                            <!-- Total Value -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Total Value</h6>
                                <strong class="text-success">${{ number_format($request->total, 2) }}</strong>
                            </div>

                            <!-- Days Waiting -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Days Waiting</h6>
                                <span class="badge bg-{{ $request->created_at->diffInDays(now()) > 3 ? 'danger' : ($request->created_at->diffInDays(now()) > 1 ? 'warning' : 'success') }}">
                                    {{ $request->created_at->diffInDays(now()) }} days
                                </span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <!-- Actions -->
                            <div class="d-flex gap-1 justify-content-center">
                                @if($request->status == config('constants.FEEDING_REQUESTED'))
                                <button class="btn btn-sm btn-outline-success"
                                    onclick="approveRequest({{ $request->id ?? 0 }})" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning"
                                    onclick="holdRequest({{ $request->id ?? 0 }})" title="Hold">
                                    <i class="fas fa-pause"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="rejectRequest({{ $request->id ?? 0 }})" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                                @elseif($request->status == config('constants.FEEDING_PROCESSING'))
                                <button class="btn btn-sm btn-outline-success"
                                    onclick="completeRequest({{ $request->id ?? 0 }})" title="Complete">
                                    <i class="fas fa-check-double"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning"
                                    onclick="holdRequest({{ $request->id ?? 0 }})" title="Hold">
                                    <i class="fas fa-pause"></i>
                                </button>
                                @else
                                <span class="text-muted">No actions available</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No feeding requests found</h5>
                        <p class="text-muted">All requests have been processed.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <span class="text-muted">Showing {{ $feedingRequests->firstItem() ?? 0 }}-{{ $feedingRequests->lastItem() ?? 0 }} of {{ $feedingRequests->total() }} requests</span>
        </div>
        <nav aria-label="Page navigation">
            {{ $feedingRequests->links() }}
        </nav>
    </div>
</div>

<script>
    function toggleView() {
        const tableView = document.getElementById('tableView');
        const gridView = document.getElementById('gridView');
        const toggleText = document.getElementById('viewToggleText');

        if (tableView.classList.contains('d-none')) {
            tableView.classList.remove('d-none');
            gridView.classList.add('d-none');
            toggleText.textContent = 'Grid View';
        } else {
            tableView.classList.add('d-none');
            gridView.classList.remove('d-none');
            toggleText.textContent = 'Table View';
        }
    }

    function selectAll() {
        const checkboxes = document.querySelectorAll('.feeding-checkbox');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    function approveRequest(requestId) {
        console.log('Approving request ID:', requestId);

        if (confirm('Are you sure you want to approve this feeding request?')) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF token not found!');
                return;
            }

            console.log('CSRF Token:', csrfToken.getAttribute('content'));
            console.log('Request URL:', `/warehouse/process-feeding-request/${requestId}`);

            fetch(`/warehouse/process-feeding-request/${requestId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'approve',
                        notes: 'Approved via dashboard'
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        alert('Request approved successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to approve request'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to approve request: ' + error.message);
                });
        }
    }

    function holdRequest(requestId) {
        console.log('Holding request ID:', requestId);

        if (confirm('Are you sure you want to put this feeding request on hold?')) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF token not found!');
                return;
            }

            console.log('CSRF Token:', csrfToken.getAttribute('content'));
            console.log('Request URL:', `/warehouse/process-feeding-request/${requestId}`);

            fetch(`/warehouse/process-feeding-request/${requestId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'hold',
                        notes: 'Put on hold via dashboard'
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        alert('Request put on hold successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to hold request'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to hold request: ' + error.message);
                });
        }
    }

    function rejectRequest(requestId) {
        console.log('Rejecting request ID:', requestId);

        const reason = prompt('Please enter the reason for rejection:');
        if (reason !== null && reason.trim() !== '') {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF token not found!');
                return;
            }

            console.log('CSRF Token:', csrfToken.getAttribute('content'));
            console.log('Request URL:', `/warehouse/process-feeding-request/${requestId}`);
            console.log('Rejection reason:', reason);

            fetch(`/warehouse/process-feeding-request/${requestId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'reject',
                        notes: 'Rejected via dashboard: ' + reason
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        alert('Request rejected successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to reject request'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to reject request: ' + error.message);
                });
        }
    }

    function completeRequest(requestId) {
        console.log('Completing request ID:', requestId);

        if (confirm('Are you sure you want to mark this feeding request as completed?')) {
            const notes = prompt('Add completion notes (optional):') || 'Completed via dashboard';

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF token not found!');
                return;
            }

            console.log('CSRF Token:', csrfToken.getAttribute('content'));
            console.log('Request URL:', `/warehouse/process-feeding-request/${requestId}`);
            console.log('Completion notes:', notes);

            fetch(`/warehouse/process-feeding-request/${requestId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'complete',
                        notes: 'Completed via dashboard: ' + notes
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        alert('Request completed successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to complete request'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to complete request: ' + error.message);
                });
        }
    }

    function bulkAction() {
        const selectedRequests = document.querySelectorAll('.feeding-checkbox:checked');
        if (selectedRequests.length === 0) {
            alert('Please select requests to perform bulk action');
            return;
        }

        const action = prompt('Select action:\n1. Approve all\n2. Hold all\n3. Reject all\n4. Complete all\n\nEnter 1, 2, 3, or 4:');

        if (!action || !['1', '2', '3', '4'].includes(action)) {
            return;
        }

        const actionMap = {
            '1': 'approve',
            '2': 'hold',
            '3': 'reject',
            '4': 'complete'
        };

        const selectedAction = actionMap[action];
        const requestIds = Array.from(selectedRequests).map(cb => cb.value);

        if (confirm(`Are you sure you want to ${selectedAction} ${requestIds.length} selected requests?`)) {
            // Show loading
            const bulkBtn = document.querySelector('button[onclick="bulkAction()"]');
            const originalText = bulkBtn.innerHTML;
            bulkBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
            bulkBtn.disabled = true;

            // Process each request
            let completed = 0;
            let errors = 0;

            requestIds.forEach((requestId, index) => {
                fetch(`/warehouse/process-feeding-request/${requestId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            action: selectedAction,
                            notes: `Bulk ${selectedAction} via dashboard`
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        completed++;
                        if (completed + errors === requestIds.length) {
                            // Reset button
                            bulkBtn.innerHTML = originalText;
                            bulkBtn.disabled = false;

                            if (errors === 0) {
                                alert(`Successfully ${selectedAction}d ${completed} requests!`);
                                location.reload();
                            } else {
                                alert(`Completed ${completed} requests with ${errors} errors.`);
                                location.reload();
                            }
                        }
                    })
                    .catch(error => {
                        errors++;
                        if (completed + errors === requestIds.length) {
                            // Reset button
                            bulkBtn.innerHTML = originalText;
                            bulkBtn.disabled = false;

                            alert(`Completed ${completed} requests with ${errors} errors.`);
                            location.reload();
                        }
                    });
            });
        }
    }

    // Select all checkbox functionality
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.feeding-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
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

    .btn-group .btn {
        border-radius: 0.375rem;
    }

    .badge {
        font-size: 0.75em;
    }
</style>

@include('warehouse.modals.new-feeding-request-modal')

@endsection
