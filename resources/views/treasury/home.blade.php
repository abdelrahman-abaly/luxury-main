@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 dashboard-header">
                <i class="fas fa-vault text-success me-2"></i>
                Treasury Dashboard
            </h2>
            <p class="text-muted mb-0">Monitor all financial transactions and revenue</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('treasury.transactions') }}" class="btn btn-primary">
                <i class="fas fa-list me-1"></i>
                View All Transactions
            </a>
            <button class="btn btn-success" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Revenue</h5>
                    <h2 class="mb-1">EGP {{ number_format($stats['total_revenue'] ?? 0, 2) }}</h2>
                    <p class="mb-0">All Time</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">This Month</h5>
                    <h2 class="mb-1">EGP {{ number_format($stats['month_revenue'] ?? 0, 2) }}</h2>
                    <p class="mb-0">{{ now()->format('F Y') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Today</h5>
                    <h2 class="mb-1">EGP {{ number_format($stats['today_revenue'] ?? 0, 2) }}</h2>
                    <p class="mb-0">{{ now()->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Pending Amount</h5>
                    <h2 class="mb-1">EGP {{ number_format($stats['pending_amount'] ?? 0, 2) }}</h2>
                    <p class="mb-0">Not Delivered Yet</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Delivered Orders</h6>
                            <h3 class="mb-0">{{ number_format($stats['delivered_orders'] ?? 0) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Average Order Value</h6>
                            <h3 class="mb-0">EGP {{ number_format($stats['average_order_value'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0 fw-bold text-success">
                    <i class="fas fa-chart-area me-2"></i>Revenue Trend
                </h5>
                <small class="text-muted">Daily revenue for the last 30 days</small>
            </div>
        </div>
        <div class="card-body p-4">
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="fas fa-dollar-sign text-success fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted small">Total (30 days)</h6>
                            <h4 class="mb-0 fw-bold">EGP {{ number_format(array_sum($chartData['data'] ?? [0]), 0) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="fas fa-chart-bar text-primary fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted small">Daily Average</h6>
                            <h4 class="mb-0 fw-bold">EGP {{ number_format(count($chartData['data'] ?? [1]) > 0 ? array_sum($chartData['data'] ?? [0]) / count($chartData['data']) : 0, 0) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                <i class="fas fa-calendar text-info fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted small">Period</h6>
                            <h4 class="mb-0 fw-bold">30 Days</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-calendar-alt me-2"></i>
                Monthly Breakdown (Last 6 Months)
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Month</th>
                            <th>Orders Delivered</th>
                            <th>Revenue</th>
                            <th>Average per Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyBreakdown as $month)
                        <tr>
                            <td><strong>{{ $month['name'] }}</strong></td>
                            <td>{{ number_format($month['orders']) }}</td>
                            <td><strong class="text-success">EGP {{ number_format($month['revenue'], 2) }}</strong></td>
                            <td>EGP {{ $month['orders'] > 0 ? number_format($month['revenue'] / $month['orders'], 2) : '0.00' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total</th>
                            <th>{{ number_format(array_sum(array_column($monthlyBreakdown, 'orders'))) }}</th>
                            <th><strong class="text-success">EGP {{ number_format(array_sum(array_column($monthlyBreakdown, 'revenue')), 2) }}</strong></th>
                            <th>-</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Recent Transactions
                </h5>
                <a href="{{ route('treasury.transactions') }}" class="btn btn-sm btn-primary">
                    View All
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
                            <th>Driver</th>
                            <th>Amount</th>
                            <th>Date Delivered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>
                                @if($order->customer)
                                {{ $order->customer->name }}
                                @else
                                <span class="text-muted">Unknown</span>
                                @endif
                            </td>
                            <td>
                                @if($order->deliveryAgent)
                                {{ $order->deliveryAgent->name }}
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td><strong class="text-success">EGP {{ number_format($order->total, 2) }}</strong></td>
                            <td>{{ $order->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No transactions yet</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // const chartData = {
    //     !!json_encode($chartData ?? ['labels' => [], 'data' => []]) !!
    // };
    const chartData = {!! json_encode($chartData ?? ['labels' => [], 'data' => []]) !!};
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart');

        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartData.labels || [],
                    datasets: [{
                        label: 'Daily Revenue (EGP)',
                        data: chartData.data || [],
                        borderColor: 'rgb(25, 135, 84)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'EGP ' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: EGP ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
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

    .shadow-sm {
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1) !important;
    }

    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }

    .table th {
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
</style>
@endsection
