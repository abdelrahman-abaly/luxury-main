@extends("layouts.main")

@section("content")
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 dashboard-header">Accepted Returns</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('warehouse.home')}}">Warehouse</a></li>
            <li class="breadcrumb-item">Returns</li>
            <li class="breadcrumb-item active">Accepted Returns</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Accepted Returns</h5>
                <h3 class="mb-1">{{number_format($stats['total_accepted'] ?? 0)}}</h3>
                <p class="mb-0">Ready for Processing</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Ready for Refund</h5>
                <h3 class="mb-1">{{number_format($stats['ready_for_refund'] ?? 0)}}</h3>
                <p class="mb-0">Over 24 Hours</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Total Value</h5>
                <h3 class="mb-1">EGP {{number_format(($stats['total_value'] ?? 0) / 1000)}}K</h3>
                <p class="mb-0">To Refund</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Avg Approval</h5>
                <h3 class="mb-1">{{number_format($stats['avg_approval_time'] ?? 0, 1)}}h</h3>
                <p class="mb-0">Time</p>
            </div>
        </div>
    </div>
</div>

<!-- Top Controls -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{route('warehouse.accepted-returns')}}">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search accepted returns..." value="{{request('search')}}">
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                <div class="col-md-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-success" id="processRefundBtn">
                            <i class="fas fa-money-bill-wave"></i> Process Refund
                        </button>
                        <button type="button" class="btn btn-outline-info" id="restockBtn">
                            <i class="fas fa-boxes"></i> Restock Items
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="row mt-3 align-items-center">
            <div class="col-md-6">
                <span class="text-muted">Total: <strong>{{ $returns->total() }} accepted returns</strong></span>
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
        <h5 class="mb-0">Accepted Returns Processing</h5>
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
                        <th>Accepted Date</th>
                        <th>Total Value</th>
                        <th>Return Reason</th>
                        <th>Days Waiting</th>
                        <th>Priority</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    <tr class="table-success">
                        <td><input type="checkbox" class="form-check-input return-checkbox" value="{{ $return->id }}"></td>
                        <td><strong>{{ $return->order_number }}</strong></td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($return->customer)
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($return->customer->name) }}&background=28a745&color=fff" class="rounded-circle me-2" width="30" height="30">
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
                        <td>{{ $return->updated_at->format('Y-m-d H:i') }}</td>
                        <td><strong>EGP {{ number_format($return->total, 2) }}</strong></td>
                        <td>
                            <span class="text-muted">{{ Str::limit($return->notes, 30) }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $return->updated_at->diffInDays(now()) > 1 ? 'bg-danger' : 'bg-warning' }}">
                                {{ $return->updated_at->diffInDays(now()) }} days
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $return->updated_at->diffInDays(now()) > 1 ? 'bg-danger' : 'bg-warning' }}">
                                {{ $return->updated_at->diffInDays(now()) > 1 ? 'High' : 'Normal' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-success" title="Complete Return" data-bs-toggle="modal" data-bs-target="#completeReturnModal{{ $return->id }}">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                <button class="btn btn-sm btn-info" title="View Details" data-bs-toggle="modal" data-bs-target="#returnDetailsModal{{ $return->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" title="Restock Items" data-bs-toggle="modal" data-bs-target="#restockModal{{ $return->id }}">
                                    <i class="fas fa-boxes"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                            No accepted returns found
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Pagination and Summary -->
<div class="d-flex justify-content-between align-items-center mt-4">
    <div>
        <span class="text-muted">Showing {{ $returns->firstItem() ?? 0 }}-{{ $returns->lastItem() ?? 0 }} of {{ $returns->total() }} returns</span>
        <br>
        <strong>Total Refund Amount: EGP {{ number_format($stats['total_value'] ?? 0, 2) }}</strong>
    </div>
    <nav aria-label="Page navigation">
        {{ $returns->links() }}
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

<!-- Complete Return Modals -->
@foreach($returns as $return)
<div class="modal fade" id="completeReturnModal{{ $return->id }}" tabindex="-1" aria-labelledby="completeReturnModalLabel{{ $return->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeReturnModalLabel{{ $return->id }}">Complete Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('warehouse.complete-return', $return->id) }}" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <p>Complete this return and process refund:</p>
                    <div class="alert alert-info">
                        <strong>Order #:</strong> {{ $return->order_number }}<br>
                        <strong>Customer:</strong> {{ $return->customer ? $return->customer->name : 'N/A' }}<br>
                        <strong>Total Value:</strong> EGP {{ number_format($return->total, 2) }}
                    </div>

                    <div class="mb-3">
                        <label for="refund_method{{ $return->id }}" class="form-label">Refund Method <span class="text-danger">*</span></label>
                        <select class="form-select" id="refund_method{{ $return->id }}" name="refund_method" required>
                            <option value="">Select refund method</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="store_credit">Store Credit</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="refund_amount{{ $return->id }}" class="form-label">Refund Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">EGP</span>
                            <input type="number" class="form-control" id="refund_amount{{ $return->id }}" name="refund_amount"
                                value="{{ $return->total }}" min="0" step="0.01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes{{ $return->id }}" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes{{ $return->id }}" name="notes" rows="3"
                            placeholder="Add any notes about this return completion..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete Return</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

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
        const checkboxes = document.querySelectorAll('.return-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Action buttons functionality
    document.querySelectorAll('.btn-success, .btn-info, .btn-danger').forEach(button => {
        button.addEventListener('click', function() {
            if (this.disabled) return;

            let action = '';
            if (this.classList.contains('btn-success')) {
                action = 'process refund for this return';
            } else if (this.classList.contains('btn-info')) {
                action = 'restock this item';
            } else if (this.classList.contains('btn-danger')) {
                action = 'send this item to damaged goods';
            }

            if (confirm(`Are you sure you want to ${action}?`)) {
                alert(`${action.charAt(0).toUpperCase() + action.slice(1)} completed successfully!`);
                // Here you would make an AJAX call to perform the action
            }
        });
    });

    // Bulk Actions
    document.getElementById('submitBulk').addEventListener('click', function() {
        const action = document.getElementById('bulkAction').value;
        const selectedReturns = document.querySelectorAll('.return-checkbox:checked');

        if (!action) {
            alert('Please select a bulk action');
            return;
        }

        if (selectedReturns.length === 0) {
            alert('Please select at least one return');
            return;
        }

        alert(`${action} will be performed on ${selectedReturns.length} selected returns`);
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