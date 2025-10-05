@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="dashboard-header mb-1">
                        <i class="fas fa-exchange-alt text-primary me-2"></i>
                        Stock Movement Report
                    </h2>
                    <p class="text-muted mb-0">Track all stock movements including ins, outs, and adjustments</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-file-export me-1"></i>
                            Export Report
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('warehouse.reports.export', ['type' => 'stock-movement', 'format' => 'excel']) }}">
                                    <i class="fas fa-file-excel text-success me-2"></i>
                                    Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('warehouse.reports.export', ['type' => 'stock-movement', 'format' => 'csv']) }}">
                                    <i class="fas fa-file-csv text-info me-2"></i>
                                    CSV
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('warehouse.reports.export', ['type' => 'stock-movement', 'format' => 'pdf']) }}">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>
                        Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.reports.stock-movement') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="product_id" class="form-label">Product</label>
                    <select class="form-select" id="product_id" name="product_id">
                        <option value="">All Products</option>
                        @foreach($products ?? [] as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="movement_type" class="form-label">Movement Type</label>
                    <select class="form-select" id="movement_type" name="movement_type">
                        <option value="">All Types</option>
                        <option value="in" {{ request('movement_type') == 'in' ? 'selected' : '' }}>Stock In</option>
                        <option value="out" {{ request('movement_type') == 'out' ? 'selected' : '' }}>Stock Out</option>
                        <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                        <option value="return" {{ request('movement_type') == 'return' ? 'selected' : '' }}>Return</option>
                        <option value="damage" {{ request('movement_type') == 'damage' ? 'selected' : '' }}>Damage</option>
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

    <!-- Movements Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at }}</td>
                            <td>{{ $movement->name }}</td>
                            <td>{{ $movement->sku }}</td>
                            <td>
                                <span class="badge bg-{{
                                            $movement->movement_type == 'in' ? 'success' :
                                            ($movement->movement_type == 'out' ? 'danger' :
                                            ($movement->movement_type == 'adjustment' ? 'info' :
                                            ($movement->movement_type == 'return' ? 'warning' : 'secondary')))
                                        }}">
                                    {{ ucfirst($movement->movement_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $movement->quantity > 0 ? 'success' : 'danger' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                </span>
                            </td>
                            <td>{{ $movement->reason }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">No stock movements found</h6>
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
            <span class="text-muted">
                Showing {{ $movements->firstItem() ?? 0 }}-{{ $movements->lastItem() ?? 0 }}
                of {{ $movements->total() }} movements
            </span>
        </div>
        <nav aria-label="Page navigation">
            {{ $movements->links() }}
        </nav>
    </div>
</div>

<style>
    @media print {

        .btn-group,
        .btn,
        .card-header button,
        .pagination {
            display: none !important;
        }
    }
</style>
@endsection
