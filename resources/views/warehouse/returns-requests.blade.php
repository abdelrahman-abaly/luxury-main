@extends("layouts.main")

@section("content")
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 dashboard-header">Returns Requests</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('warehouse.home')}}">Warehouse</a></li>
            <li class="breadcrumb-item">Returns</li>
            <li class="breadcrumb-item active">Returns Requests</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Total Requests</h5>
                <h3 class="mb-1">{{number_format($stats['total_requests'] ?? 0)}}</h3>
                <p class="mb-0">All Status</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Pending</h5>
                <h3 class="mb-1">{{number_format($stats['pending_requests'] ?? 0)}}</h3>
                <p class="mb-0">Awaiting Review</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Approved</h5>
                <h3 class="mb-1">{{number_format($stats['approved_requests'] ?? 0)}}</h3>
                <p class="mb-0">Accepted</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Rejected</h5>
                <h3 class="mb-1">{{number_format($stats['rejected_requests'] ?? 0)}}</h3>
                <p class="mb-0">Declined</p>
            </div>
        </div>
    </div>
</div>

<!-- Top Controls -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{route('warehouse.returns-requests')}}">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search return requests..." value="{{request('search')}}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="return_requested" {{request('status') == 'return_requested' ? 'selected' : ''}}>Requested</option>
                        <option value="return_pending" {{request('status') == 'return_pending' ? 'selected' : ''}}>Pending</option>
                        <option value="return_approved" {{request('status') == 'return_approved' ? 'selected' : ''}}>Approved</option>
                        <option value="return_rejected" {{request('status') == 'return_rejected' ? 'selected' : ''}}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" placeholder="From Date" value="{{request('date_from')}}">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" placeholder="To Date" value="{{request('date_to')}}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{route('warehouse.returns-requests')}}" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh"></i>
                    </a>
                </div>
            </div>
        </form>

        <div class="row mt-3 align-items-center">
            <div class="col-md-6">
                <span class="text-muted">Total: <strong>{{ $returns->total() }} requests</strong></span>
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
        <h5 class="mb-0">Customer Return Requests</h5>
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
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Total Value</th>
                        <th>Return Reason</th>
                        <th>Days Waiting</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    <tr class="{{ $return->status == config('constants.RETURN_REJECTED') ? 'table-danger' : ($return->status == config('constants.RETURN_APPROVED') ? 'table-success' : 'table-warning') }}">
                        <td><input type="checkbox" class="form-check-input request-checkbox" value="{{ $return->id }}"></td>
                        <td><strong>{{ $return->order_number }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($return->customer)
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($return->customer->name) }}&background=007bff&color=fff" class="rounded-circle me-2" width="30" height="30">
                                <div>
                                    <strong>{{ $return->customer->name }}</strong>
                                </div>
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
                            @php
                            $statusColors = [
                            config('constants.RETURN_REQUESTED') => 'bg-warning',
                            config('constants.RETURN_PENDING') => 'bg-info',
                            config('constants.RETURN_APPROVED') => 'bg-success',
                            config('constants.RETURN_REJECTED') => 'bg-danger'
                            ];
                            $statusColor = $statusColors[$return->status] ?? 'bg-secondary';

                            $statusTexts = [
                            config('constants.RETURN_REQUESTED') => 'Return Requested',
                            config('constants.RETURN_PENDING') => 'Return Pending',
                            config('constants.RETURN_APPROVED') => 'Return Approved',
                            config('constants.RETURN_REJECTED') => 'Return Rejected'
                            ];
                            $statusText = $statusTexts[$return->status] ?? 'Unknown';
                            @endphp
                            <span class="badge {{ $statusColor }}">{{ $statusText }}</span>
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
                            <div class="btn-group" role="group">
                                @if($return->status == config('constants.RETURN_REQUESTED') || $return->status == config('constants.RETURN_PENDING'))
                                <button class="btn btn-sm btn-success" title="Approve Return" data-bs-toggle="modal" data-bs-target="#approveReturnModal{{ $return->id }}">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" title="Reject Return" data-bs-toggle="modal" data-bs-target="#rejectReturnModal{{ $return->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                                <button class="btn btn-sm btn-info" title="View Details" data-bs-toggle="modal" data-bs-target="#returnDetailsModal{{ $return->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 text-muted"></i><br>
                            No return requests found
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
        <h5 class="mb-0">Grid View - Return Requests</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($returns as $return)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card grid-item {{ $return->status == config('constants.RETURN_REJECTED') ? 'border-danger' : ($return->status == config('constants.RETURN_APPROVED') ? 'border-success' : 'border-warning') }}">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/300x200/{{ $return->status == config('constants.RETURN_APPROVED') ? '28a745' : ($return->status == config('constants.RETURN_REJECTED') ? 'dc3545' : 'ffc107') }}/ffffff?text={{ substr($return->order_number, -3) }}" class="card-img-top" alt="Return Order">
                        <div class="position-absolute top-0 end-0 p-2">
                            <input type="checkbox" class="form-check-input request-checkbox" value="{{ $return->id }}">
                        </div>
                        <div class="position-absolute top-0 start-0 p-2">
                            @php
                            $priorityColors = [
                            config('constants.RETURN_REQUESTED') => 'bg-warning',
                            config('constants.RETURN_PENDING') => 'bg-info',
                            config('constants.RETURN_APPROVED') => 'bg-success',
                            config('constants.RETURN_REJECTED') => 'bg-danger'
                            ];
                            $priorityTexts = [
                            config('constants.RETURN_REQUESTED') => 'High Priority',
                            config('constants.RETURN_PENDING') => 'Medium Priority',
                            config('constants.RETURN_APPROVED') => 'Approved',
                            config('constants.RETURN_REJECTED') => 'Rejected'
                            ];
                            $priorityColor = $priorityColors[$return->status] ?? 'bg-secondary';
                            $priorityText = $priorityTexts[$return->status] ?? 'Unknown';
                            @endphp
                            <span class="badge {{ $priorityColor }}">{{ $priorityText }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">{{ $return->order_number }}</h6>
                        <p class="card-text">
                            <strong>{{ $return->customer ? $return->customer->name : 'N/A' }}</strong><br>
                            <strong>Phone:</strong> {{ $return->customer ? $return->customer->phone : 'N/A' }}<br>
                            <span class="text-muted">{{ Str::limit($return->notes, 30) }}</span><br>
                            @php
                            $statusColors = [
                            config('constants.RETURN_REQUESTED') => 'bg-warning',
                            config('constants.RETURN_PENDING') => 'bg-info',
                            config('constants.RETURN_APPROVED') => 'bg-success',
                            config('constants.RETURN_REJECTED') => 'bg-danger'
                            ];
                            $statusTexts = [
                            config('constants.RETURN_REQUESTED') => 'Return Requested',
                            config('constants.RETURN_PENDING') => 'Return Pending',
                            config('constants.RETURN_APPROVED') => 'Return Approved',
                            config('constants.RETURN_REJECTED') => 'Return Rejected'
                            ];
                            $statusColor = $statusColors[$return->status] ?? 'bg-secondary';
                            $statusText = $statusTexts[$return->status] ?? 'Unknown';
                            @endphp
                            <span class="badge {{ $statusColor }}">{{ $statusText }}</span>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-bold">EGP {{ number_format($return->total, 2) }}</span>
                            <span class="badge {{ $return->created_at->diffInDays(now()) > 3 ? 'bg-danger' : 'bg-warning' }}">
                                {{ $return->created_at->diffInDays(now()) }} days
                            </span>
                        </div>
                        <div class="btn-group w-100" role="group">
                            @if($return->status == config('constants.RETURN_REQUESTED') || $return->status == config('constants.RETURN_PENDING'))
                            <button class="btn btn-success btn-sm" title="Approve Return" data-bs-toggle="modal" data-bs-target="#approveReturnModal{{ $return->id }}">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn btn-danger btn-sm" title="Reject Return" data-bs-toggle="modal" data-bs-target="#rejectReturnModal{{ $return->id }}">
                                <i class="fas fa-times"></i> Reject
                            </button>
                            @endif
                            <button class="btn btn-info btn-sm" title="View Details" data-bs-toggle="modal" data-bs-target="#returnDetailsModal{{ $return->id }}">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-undo fa-2x mb-2 text-info"></i><br>
                    No return requests found
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Pagination and Summary -->
<div class="d-flex justify-content-between align-items-center mt-4">
    <div>
        <span class="text-muted">Showing 1-11 of 11 requests</span>
        <br>
        <strong>Total Request Value: EGP 60,500</strong>
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
        const checkboxes = document.querySelectorAll('.request-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Action buttons functionality
    document.querySelectorAll('.btn-success, .btn-danger, .btn-info').forEach(button => {
        button.addEventListener('click', function() {
            if (this.disabled) return;

            let action = '';
            if (this.classList.contains('btn-success')) {
                action = 'approve this return request';
            } else if (this.classList.contains('btn-danger')) {
                action = 'reject this return request';
            } else if (this.classList.contains('btn-info')) {
                action = 'send this request to Waiting Returns';
            }

            if (confirm(`Are you sure you want to ${action}?`)) {
                alert(`Request has been ${action.split(' ')[0]}ed successfully!`);
                // Here you would make an AJAX call to perform the action
            }
        });
    });

    // Bulk Actions
    document.getElementById('submitBulk').addEventListener('click', function() {
        const action = document.getElementById('bulkAction').value;
        const selectedRequests = document.querySelectorAll('.request-checkbox:checked');

        if (!action) {
            alert('Please select a bulk action');
            return;
        }

        if (selectedRequests.length === 0) {
            alert('Please select at least one request');
            return;
        }

        alert(`${action} will be performed on ${selectedRequests.length} selected requests`);
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
