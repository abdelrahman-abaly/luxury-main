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
                        Warehouse Analytics
                    </h2>
                    <p class="text-muted mb-0">Real-time analytics and insights for warehouse performance</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="refreshData()">
                        <i class="fas fa-sync-alt me-1"></i>
                        Refresh Data
                    </button>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>
                        Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-primary">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">{{ $kpis['fulfillment_rate']['value'] }}%</h3>
                            <small class="text-muted">Order Fulfillment Rate</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-{{ $kpis['fulfillment_rate']['trend_class'] }} me-2">
                            <i class="fas fa-arrow-{{ $kpis['fulfillment_rate']['trend_type'] }}"></i>
                            {{ abs($kpis['fulfillment_rate']['trend']) }}%
                        </span>
                        <small class="text-muted">vs last month</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-success">
                            <i class="fas fa-sync fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">{{ $kpis['turnover_rate']['value'] }}</h3>
                            <small class="text-muted">Inventory Turnover</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-{{ $kpis['turnover_rate']['trend_class'] }} me-2">
                            <i class="fas fa-arrow-{{ $kpis['turnover_rate']['trend_type'] }}"></i>
                            {{ abs($kpis['turnover_rate']['trend']) }}
                        </span>
                        <small class="text-muted">vs last month</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-warning">
                            <i class="fas fa-undo fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">{{ $kpis['return_rate']['value'] }}%</h3>
                            <small class="text-muted">Return Rate</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-{{ $kpis['return_rate']['trend_class'] }} me-2">
                            <i class="fas fa-arrow-{{ $kpis['return_rate']['trend_type'] }}"></i>
                            {{ abs($kpis['return_rate']['trend']) }}%
                        </span>
                        <small class="text-muted">vs last month</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">{{ $kpis['damage_rate']['value'] }}%</h3>
                            <small class="text-muted">Damage Rate</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-{{ $kpis['damage_rate']['trend_class'] }} me-2">
                            <i class="fas fa-arrow-{{ $kpis['damage_rate']['trend_type'] }}"></i>
                            {{ abs($kpis['damage_rate']['trend']) }}%
                        </span>
                        <small class="text-muted">vs last month</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trends Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">30-Day Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights and Predictions -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Business Insights</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($insights as $insight)
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="fa-stack">
                                        <i class="fas fa-circle fa-stack-2x text-{{ $insight['type'] }} opacity-25"></i>
                                        <i class="fas fa-{{ $insight['icon'] }} fa-stack-1x text-{{ $insight['type'] }}"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $insight['title'] }}</h6>
                                    <p class="mb-0 text-muted">{{ $insight['message'] }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6 class="text-muted">No insights to display</h6>
                            <p class="text-muted small mb-0">All metrics are within normal ranges</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Predictions & Forecasts</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-shopping-cart text-primary me-2"></i>
                                    <h6 class="mb-0">Expected Orders (7 days)</h6>
                                </div>
                                <h3 class="mb-0">{{ $predictions['expected_orders'] }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-undo text-warning me-2"></i>
                                    <h6 class="mb-0">Expected Returns (7 days)</h6>
                                </div>
                                <h3 class="mb-0">{{ $predictions['expected_returns'] }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-clock text-info me-2"></i>
                                    <h6 class="mb-0">Days Until Reorder</h6>
                                </div>
                                <h3 class="mb-0">{{ $predictions['days_until_reorder'] ?? 'N/A' }}</h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-chart-line text-success me-2"></i>
                                    <h6 class="mb-0">Stock Depletion Rate</h6>
                                </div>
                                <h3 class="mb-0">{{ $predictions['stock_depletion_rate'] }}/day</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize trends chart
    const ctx = document.getElementById('trendsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($trends['dates']),
            datasets: [{
                label: 'Orders',
                data: @json($trends['orders']),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }, {
                label: 'Returns',
                data: @json($trends['returns']),
                borderColor: 'rgb(255, 159, 64)',
                tension: 0.1
            }, {
                label: 'Stock Level',
                data: @json($trends['stock']),
                borderColor: 'rgb(54, 162, 235)',
                tension: 0.1
            }, {
                label: 'Damages',
                data: @json($trends['damages']),
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
                    text: '30-Day Performance Trends'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Refresh data
    function refreshData() {
        location.reload();
    }
</script>

<style>
    @media print {
        .btn {
            display: none !important;
        }
    }

    .fa-stack {
        font-size: 1.25em;
    }

    .border {
        border-color: rgba(0, 0, 0, 0.125) !important;
    }
</style>
@endsection
