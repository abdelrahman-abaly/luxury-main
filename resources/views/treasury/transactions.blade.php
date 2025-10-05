@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="dashboard-header mb-1">
                        <i class="fas fa-receipt text-success me-2"></i>
                        Treasury Transactions
                    </h2>
                    <p class="text-muted mb-0">All delivered orders and revenue details</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('treasury.home') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Dashboard
                    </a>
                    <form method="POST" action="{{ route('treasury.export') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-file-excel me-1"></i>
                            Export CSV
                        </button>
                    </form>
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
                            <h4 class="mb-0">EGP {{ number_format($stats['total_amount'] ?? 0, 0) }}</h4>
                            <small>Total Revenue</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-dollar-sign"></i>
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
                            <h4 class="mb-0">EGP {{ number_format($stats['filtered_amount'] ?? 0, 0) }}</h4>
                            <small>Filtered Amount</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-filter"></i>
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
                            <h4 class="mb-0">{{ number_format($stats['filtered_count'] ?? 0) }}</h4>
                            <small>Transactions</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-receipt"></i>
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
                            <h4 class="mb-0">EGP {{ number_format($stats['today_amount'] ?? 0, 0) }}</h4>
                            <small>Today's Revenue</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('treasury.transactions') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Order #, Customer...">
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
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

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Transactions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Driver</th>
                            <th>Employee</th>
                            <th>Governorate</th>
                            <th>Amount</th>
                            <th>Date Delivered</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>
                                @if($order->customer)
                                <div>
                                    <strong>{{ $order->customer->name }}</strong><br>
                                    <small class="text-muted">{{ $order->customer->phone_numbers }}</small>
                                </div>
                                @else
                                <span class="text-muted">Unknown</span>
                                @endif
                            </td>
                            <td>
                                @if($order->deliveryAgent)
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    {{ $order->deliveryAgent->name }}
                                </div>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($order->employee)
                                {{ $order->employee->name }}
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td><span class="badge bg-info">{{ $order->governorate ?? 'N/A' }}</span></td>
                            <td><strong class="text-success">EGP {{ number_format($order->total, 2) }}</strong></td>
                            <td>{{ $order->updated_at->format('d M Y') }}</td>
                            <td><small class="text-muted">{{ $order->updated_at->format('h:i A') }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No transactions found</h5>
                                <p class="text-muted">Try adjusting your filters</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($orders->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">Page Total:</th>
                            <th><strong class="text-success">EGP {{ number_format($orders->sum('total'), 2) }}</strong></th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        @if($orders->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} transactions
                </div>
                <nav aria-label="Page navigation">
                    {{ $orders->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

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
