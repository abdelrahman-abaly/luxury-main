@extends('layouts.main')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="dashboard-header mb-1">
                        <i class="fas fa-door-open text-primary me-2"></i>
                        Exit Permission Management
                    </h2>
                    <p class="text-muted mb-0">Manage exit permissions for orders and deliveries</p>
                    @if(($readyOrders ?? collect())->isEmpty())
                    <div class="alert alert-warning mt-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>No orders available for exit permission:</strong> All OUT_FOR_DELIVERY orders either have no products or contain out-of-stock items.
                    </div>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="toggleView()">
                        <i class="fas fa-th-large me-1"></i>
                        <span id="viewToggleText">Grid View</span>
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newExitPermissionModal">
                        <i class="fas fa-plus me-1"></i>
                        New Permission
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['total_permissions'] ?? 0 }}</h4>
                            <small>Total Permissions</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-door-open"></i>
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
                            <h4 class="mb-0">{{ $stats['pending_approval'] ?? 0 }}</h4>
                            <small>Pending Approval</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-clock"></i>
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
                            <h4 class="mb-0">{{ $stats['approved_permissions'] ?? 0 }}</h4>
                            <small>Approved</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-check"></i>
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
            <form method="GET" action="{{ route('warehouse.exit-permission') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                        value="{{ request('search') }}" placeholder="Search by order number, customer name, or phone">
                </div>
                <div class="col-md-2">
                    <label for="permission_status" class="form-label">Status</label>
                    <select class="form-select" id="permission_status" name="permission_status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('permission_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('permission_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('permission_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                <h5 class="mb-0">Exit Permissions</h5>
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
                            <th>Delivery Agent</th>
                            <th>Request Date</th>
                            <th>Total Value</th>
                            <th>Status</th>
                            <th>Days Waiting</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exitPermissions as $permission)
                        <tr class="table-{{ $permission->status == 'shipped' ? 'success' : 'warning' }}">
                            <td>
                                <input type="checkbox" class="form-check-input permission-checkbox" value="{{ $permission->id }}">
                            </td>
                            <td><strong>{{ $permission->order_number }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $permission->customer->name ?? 'Unknown Customer' }}</strong><br>
                                    <small class="text-muted">{{ $permission->customer->phone ?? 'No phone' }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    @foreach($permission->products->take(2) as $product)
                                    <span class="badge bg-info me-1">{{ $product->name }}</span>
                                    @endforeach
                                    @if($permission->products->count() > 2)
                                    <span class="badge bg-secondary">+{{ $permission->products->count() - 2 }} more</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($permission->deliveryAgent)
                                <span class="badge bg-primary">{{ $permission->deliveryAgent->name }}</span>
                                @else
                                <span class="badge bg-secondary">Not Assigned</span>
                                @endif
                            </td>
                            <td>{{ $permission->created_at->format('M d, Y') }}</td>
                            <td>EGP {{ number_format($permission->total, 2) }}</td>
                            <td>
                                @php
                                $statusText = '';
                                $statusClass = '';
                                switch($permission->status) {
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
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $permission->created_at->diffInDays(now()) > 3 ? 'danger' : ($permission->created_at->diffInDays(now()) > 1 ? 'warning' : 'success') }}">
                                    {{ $permission->created_at->diffInDays(now()) }} days
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if($permission->status == '17') {{-- Show approve button only if requested --}}
                                    <button class="btn btn-sm btn-outline-success"
                                        onclick="approvePermission({{ $permission->id ?? 0 }})" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                    @if($permission->status == '17') {{-- Show reject button only if requested --}}
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="rejectPermission({{ $permission->id ?? 0 }})" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                    @if($permission->status == '18') {{-- Show ship button only if approved --}}
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="shipPermission({{ $permission->id ?? 0 }})" title="Ship">
                                        <i class="fas fa-shipping-fast"></i>
                                    </button>
                                    @endif
                                    <button class="btn btn-sm btn-outline-info"
                                        onclick="viewPermission({{ $permission->id ?? 0 }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No exit permissions found</h5>
                                <p class="text-muted">All permissions have been processed.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <span class="text-muted">Showing {{ $exitPermissions->firstItem() ?? 0 }}-{{ $exitPermissions->lastItem() ?? 0 }} of {{ $exitPermissions->total() }} permissions</span>
        </div>
        <nav aria-label="Page navigation">
            {{ $exitPermissions->links() }}
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
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    function approvePermission(permissionId) {
        if (!permissionId || permissionId === 0) {
            alert('Invalid permission ID');
            return;
        }

        if (confirm('Are you sure you want to approve this exit permission?')) {
            processPermission(permissionId, 'approve');
        }
    }

    function rejectPermission(permissionId) {
        if (!permissionId || permissionId === 0) {
            alert('Invalid permission ID');
            return;
        }

        const reason = prompt('Please provide a reason for rejection:');
        if (reason === null) return; // User cancelled

        if (reason.trim() === '') {
            alert('Please provide a reason for rejection');
            return;
        }

        processPermission(permissionId, 'reject', reason);
    }

    function shipPermission(permissionId) {
        if (!permissionId || permissionId === 0) {
            alert('Invalid permission ID');
            return;
        }

        if (confirm('Are you sure you want to ship this exit permission?')) {
            processPermission(permissionId, 'ship');
        }
    }

    function viewPermission(permissionId) {
        if (!permissionId || permissionId === 0) {
            alert('Invalid permission ID');
            return;
        }

        // Open modal or redirect to detailed view
        window.open(`/warehouse/exit-permission/${permissionId}/details`, '_blank');
    }

    function processPermission(permissionId, action, notes = '') {
        console.log('Processing permission:', {
            permissionId,
            action,
            notes
        });

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('action', action);
        if (notes) {
            formData.append('notes', notes);
        }

        console.log('Form data:', Object.fromEntries(formData));
        console.log('URL:', `/warehouse/process-exit-permission/${permissionId}`);

        fetch(`/warehouse/process-exit-permission/${permissionId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'Permission processed successfully');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', data.message || 'Failed to process permission');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred while processing the request');
            });
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed"
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', alertHtml);

        // Auto remove after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    function bulkAction() {
        const selectedPermissions = document.querySelectorAll('.permission-checkbox:checked');
        if (selectedPermissions.length === 0) {
            alert('Please select permissions to perform bulk action');
            return;
        }

        const action = prompt('Choose action:\n1. Approve all\n2. Reject all\n\nEnter 1 or 2:');

        if (action === null) return; // User cancelled

        let actionType = '';
        let notes = '';

        if (action === '1') {
            actionType = 'approve';
            if (!confirm(`Are you sure you want to approve ${selectedPermissions.length} permissions?`)) {
                return;
            }
        } else if (action === '2') {
            actionType = 'reject';
            notes = prompt('Please provide a reason for rejection:');
            if (notes === null) return; // User cancelled
            if (notes.trim() === '') {
                alert('Please provide a reason for rejection');
                return;
            }
        } else {
            alert('Invalid action. Please enter 1 or 2.');
            return;
        }

        // Process all selected permissions
        const permissionIds = Array.from(selectedPermissions).map(cb => cb.value);
        processBulkPermissions(permissionIds, actionType, notes);
    }

    function processBulkPermissions(permissionIds, action, notes = '') {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('action', action);
        formData.append('permission_ids', JSON.stringify(permissionIds));
        if (notes) {
            formData.append('notes', notes);
        }

        fetch('/warehouse/bulk-process-exit-permissions', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || `${permissionIds.length} permissions processed successfully`);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', data.message || 'Failed to process permissions');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred while processing the request');
            });
    }

    // Select all checkbox functionality
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Individual checkbox change handler
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('permission-checkbox')) {
            const allCheckboxes = document.querySelectorAll('.permission-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.permission-checkbox:checked');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');

            if (checkedCheckboxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCheckboxes.length === allCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }
    });

    // Auto-refresh page every 5 minutes to keep data updated
    setInterval(function() {
        // Only refresh if no modals are open
        if (!document.querySelector('.modal.show')) {
            location.reload();
        }
    }, 300000); // 5 minutes
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

    .alert {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .btn-group .btn {
        margin-right: 2px;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .permission-checkbox:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>

@include('warehouse.modals.new-exit-permission-modal')
</div>
@endsection