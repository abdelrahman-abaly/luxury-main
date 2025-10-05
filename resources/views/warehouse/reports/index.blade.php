@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="dashboard-header mb-1">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Warehouse Reports
                    </h2>
                    <p class="text-muted mb-0">Comprehensive analytics and insights for warehouse operations</p>
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ number_format($stats['total_products']) }}</h4>
                            <small>Total Products</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-box"></i>
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
                            <h4 class="mb-0">EGP {{ number_format($stats['total_value'], 2) }}</h4>
                            <small>Total Stock Value</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-dollar-sign"></i>
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
                            <h4 class="mb-0">{{ number_format($stats['damaged_items']) }}</h4>
                            <small>Damaged Items</small>
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
                            <h4 class="mb-0">{{ number_format($stats['pending_returns']) }}</h4>
                            <small>Pending Returns</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-undo"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Movement Trends -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Stock Movement Trends</h5>
        </div>
        <div class="card-body">
            <canvas id="stockTrendsChart" height="100"></canvas>
        </div>
    </div>

    <!-- Top Products -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Top Performing Products</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Total Quantity</th>
                                    <th>Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ number_format($product->total_quantity) }}</td>
                                    <td>EGP {{ number_format($product->total_value, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">No products found</h6>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Stock Alerts</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($stockAlerts as $alert)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $alert->name }}</h6>
                                <small class="text-danger">{{ $alert->stock_quantity }} units</small>
                            </div>
                            <p class="mb-1">SKU: {{ $alert->sku }}</p>
                            <small class="text-muted">Reorder Point: {{ $alert->reorder_point }}</small>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6 class="text-muted">No stock alerts</h6>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Types -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-exchange-alt fa-3x text-primary mb-3"></i>
                    <h5>Stock Movement Report</h5>
                    <p class="text-muted">Track all stock movements including ins, outs, and adjustments</p>
                    <a href="{{ route('warehouse.reports.stock-movement') }}" class="btn btn-primary">
                        View Report
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5>Damaged Items Report</h5>
                    <p class="text-muted">Analyze damaged inventory and loss values</p>
                    <a href="{{ route('warehouse.reports.damaged-items') }}" class="btn btn-danger">
                        View Report
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-undo fa-3x text-warning mb-3"></i>
                    <h5>Returns Report</h5>
                    <p class="text-muted">Track return rates and analyze return reasons</p>
                    <a href="{{ route('warehouse.reports.returns') }}" class="btn btn-warning">
                        View Report
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-tools fa-3x text-info mb-3"></i>
                    <h5>Repairing Orders Report</h5>
                    <p class="text-muted">Monitor repair orders and their status</p>
                    <a href="{{ route('warehouse.reports.repairing-orders') }}" class="btn btn-info">
                        View Report
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                    <h5>Performance Report</h5>
                    <p class="text-muted">Analyze warehouse performance metrics</p>
                    <a href="{{ route('warehouse.reports.performance') }}" class="btn btn-success">
                        View Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize stock trends chart
    const ctx = document.getElementById('stockTrendsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($trends['dates']),
            datasets: [{
                label: 'Stock Levels',
                data: @json($trends['stock_levels']),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }, {
                label: 'Orders',
                data: @json($trends['order_counts']),
                borderColor: 'rgb(54, 162, 235)',
                tension: 0.1
            }, {
                label: 'Returns',
                data: @json($trends['return_counts']),
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: '30-Day Trends'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<style>
    @media print {

        .btn-group,
        .btn {
            display: none !important;
        }
    }
</style>
@endsection
