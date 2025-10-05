@extends("layouts.main")

<style>
    /* body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #0d6efd;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 20px 0;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        .profile-section {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 10px;
        }

        .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.3);
            margin-bottom: 10px;
        }

        .profile-section h5 {
            color: white;
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .profile-section small {
            color: rgba(255,255,255,0.7);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: white;
        }

        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            font-weight: 600;
        }

        .sidebar .nav-link i.fa-home,
        .sidebar .nav-link i.fa-warehouse,
        .sidebar .nav-link i.fa-boxes,
        .sidebar .nav-link i.fa-truck,
        .sidebar .nav-link i.fa-undo,
        .sidebar .nav-link i.fa-clipboard-list,
        .sidebar .nav-link i.fa-cubes,
        .sidebar .nav-link i.fa-box,
        .sidebar .nav-link i.fa-exclamation-triangle,
        .sidebar .nav-link i.fa-comments,
        .sidebar .nav-link i.fa-phone,
        .sidebar .nav-link i.fa-envelope {
            margin-right: 10px;
            width: 20px;
        }

        .nav-link-content {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .nav-link-badge {
            background: rgba(255,255,255,0.3);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: auto;
        }

        .collapse .nav-link {
            font-size: 0.9rem;
            padding: 10px 20px 10px 50px;
        }

        .main-content {
            margin-left: 280px;
            padding: 0;
        }

        .top-header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
        }

        .content-wrapper {
            padding: 30px;
        }

        .dashboard-header {
            color: #2c3e50;
            font-weight: 600;
        }

        .stat-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }

        .stat-card .card-body {
            padding: 25px;
        } */

    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    .mini-stat {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s;
        border: 2px solid transparent;
        height: 100%;
    }

    .mini-stat:hover {
        background: white;
        border-color: #0d6efd;
        transform: translateY(-3px);
    }

    .mini-stat-icon {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .mini-stat-value {
        font-size: 1.8rem;
        font-weight: bold;
        margin: 10px 0;
    }

    .mini-stat-label {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead {
        background: #f8f9fa;
    }

    .table th {
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
        }

        .main-content {
            margin-left: 0;
        }
    }
</style>
@section("content")
<!-- Warehouse Home Content -->
<div id="warehouse-home">
    <!-- Header with User Info -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 dashboard-header">Warehouse Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, Mostafa Mounir - Warehouse Manager</p>
        </div>
        <!-- <div class="d-flex align-items-center">
                <div class="me-3">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Profile" class="rounded-circle" width="40" height="40">
                    <span class="ms-2">Mostafa Mounir</span>
                    <small class="text-muted d-block">Warehouse</small>
                </div>
                <div class="position-relative">
                    <i class="fas fa-bell text-primary fs-4"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        5
                    </span>
                </div>
            </div> -->
    </div>

    <!-- Performance Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i>Performance Overview
                </h5>
                <small class="text-muted">Warehouse send out items tracking</small>
            </div>
            <div class="d-flex align-items-center">
                <select class="form-select form-select-sm me-2" id="performancePeriod">
                    <option value="this_month" selected>This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="custom">Custom Range</option>
                </select>
                <input type="date" class="form-control form-control-sm me-1" id="dateFrom" style="display: none;">
                <input type="date" class="form-control form-control-sm" id="dateTo" style="display: none;">
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Chart Container -->
            <div style="position: relative; height: 400px; width: 100%;">
                <canvas id="sendOutChart"></canvas>
            </div>
        </div>

        <!-- Summary Cards Below Chart -->
        <div class="card-footer bg-light">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="fas fa-box text-primary fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted small">Total Items Sent</h6>
                            <h4 class="mb-0 fw-bold">{{ array_sum($chartData['data'] ?? [0]) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="fas fa-dollar-sign text-success fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted small">Total Value</h6>
                            <h4 class="mb-0 fw-bold">EGP {{ number_format(array_sum($chartData['values'] ?? [0]), 0) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                <i class="fas fa-chart-bar text-info fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted small">Daily Average</h6>
                            <h4 class="mb-0 fw-bold">{{ count($chartData['data'] ?? [1]) > 0 ? round(array_sum($chartData['data'] ?? [0]) / count($chartData['data']), 1) : 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                <i class="fas fa-calendar-day text-warning fs-5"></i>
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

    <!-- Summary Cards Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Orders</h5>
                    <h3 class="mb-1">{{number_format($stats['total_orders'] ?? 0)}}</h3>
                    <p class="mb-0">{{number_format(($stats['total_revenue'] ?? 0) / 1000)}}K EGP</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Delivered Orders</h5>
                    <h3 class="mb-1">{{number_format($stats['delivered_orders'] ?? 0)}}</h3>
                    <p class="mb-0">{{number_format(($stats['total_revenue'] ?? 0) / 1000)}}K EGP</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Pending Orders</h5>
                    <h3 class="mb-1">{{number_format($stats['pending_orders'] ?? 0)}}</h3>
                    <p class="mb-0">Waiting for Processing</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Today's Orders</h5>
                    <h3 class="mb-1">{{number_format($stats['today_orders'] ?? 0)}}</h3>
                    <p class="mb-0">New Orders Today</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Overview Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">In Stock</h5>
                    <h3 class="mb-1">{{number_format($stats['in_stock_products'] ?? 0)}}</h3>
                    <p class="mb-0">Products Available</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Almost Out</h5>
                    <h3 class="mb-1">{{number_format($almostOutOfStockCount ?? 0)}}</h3>
                    <p class="mb-0">Low Stock Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Out of Stock</h5>
                    <h3 class="mb-1">{{number_format($stats['out_of_stock_products'] ?? 0)}}</h3>
                    <p class="mb-0">Need Restocking</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Products</h5>
                    <h3 class="mb-1">{{number_format($stats['total_products'] ?? 0)}}</h3>
                    <p class="mb-0">All Products</p>
                </div>
            </div>
        </div>
    </div>




    <!-- Orders Summary Section -->
    <!-- <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Orders Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="p-3 bg-warning bg-opacity-10 rounded text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-clock text-warning fs-3 me-2"></i>
                                <div>
                                    <h4 class="mb-0 text-warning">{{$waitingOrders}}</h4>
                                    <small class="text-muted">items</small>
                                </div>
                            </div>
                            <h6 class="mb-0">Waiting Orders</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-info bg-opacity-10 rounded text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-shopping-cart text-info fs-3 me-2"></i>
                                <div>
                                    <h4 class="mb-0 text-info">{{$waitingPurchases}}</h4>
                                    <small class="text-muted">items</small>
                                </div>
                            </div>
                            <h6 class="mb-0">Waiting for Purchases</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-success bg-opacity-10 rounded text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-check-circle text-success fs-3 me-2"></i>
                                <div>
                                    <h4 class="mb-0 text-success">{{$acceptedOrders}}</h4>
                                    <small class="text-muted">items</small>
                                </div>
                            </div>
                            <h6 class="mb-0">Accepted Orders</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-truck text-primary fs-3 me-2"></i>
                                <div>
                                    <h4 class="mb-0 text-primary">{{$sentToManager}}</h4>
                                    <small class="text-muted">items</small>
                                </div>
                            </div>
                            <h6 class="mb-0">Sent to Moving Manager</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

    <!-- Orders Summary -->
    <div class="section-card">
        <h5 class="section-title">Orders Summary</h5>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="mini-stat">
                    <i class="fas fa-clock text-warning mini-stat-icon"></i>
                    <div class="mini-stat-value text-warning">30</div>
                    <div class="mini-stat-label">Waiting Orders</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat">
                    <i class="fas fa-shopping-cart text-info mini-stat-icon"></i>
                    <div class="mini-stat-value text-info">10</div>
                    <div class="mini-stat-label">Waiting for Purchases</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat">
                    <i class="fas fa-check-circle text-success mini-stat-icon"></i>
                    <div class="mini-stat-value text-success">50</div>
                    <div class="mini-stat-label">Accepted Orders</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat">
                    <i class="fas fa-truck text-primary mini-stat-icon"></i>
                    <div class="mini-stat-value text-primary">60</div>
                    <div class="mini-stat-label">Sent to Moving Manager</div>
                </div>
            </div>
        </div>
    </div>
    <!-- </div>
</div> -->

    <!-- Returns & Feeding -->
    <!-- <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="section-card">
                        <h5 class="section-title">Returns</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="mini-stat">
                                    <div class="mini-stat-value text-warning">30</div>
                                    <div class="mini-stat-label">Waiting Returns</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mini-stat">
                                    <div class="mini-stat-value text-info">10</div>
                                    <div class="mini-stat-label">Returns Requests</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mini-stat">
                                    <div class="mini-stat-value text-success">15</div>
                                    <div class="mini-stat-label">Accepted Returns</div>
                                    <small class="text-muted">(today)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="section-card">
                        <h5 class="section-title">Feeding Requests</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mini-stat border border-danger">
                                    <i class="fas fa-bell text-danger mini-stat-icon"></i>
                                    <div class="mini-stat-value text-danger">1</div>
                                    <div class="mini-stat-label">Feeding Requests</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mini-stat border border-danger">
                                    <i class="fas fa-sign-out-alt text-danger mini-stat-icon"></i>
                                    <div class="mini-stat-value text-danger">1</div>
                                    <div class="mini-stat-label">Exit Permission</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

    <!-- Recent Orders -->
    <div class="section-card">
        <h5 class="section-title">Recent Orders</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Employee</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td>{{ $order['order_number'] }}</td>
                        <td>{{ $order['customer_name'] }}</td>
                        <td>{{ $order['employee_name'] }}</td>
                        <td>
                            <span class="badge bg-{{ $order['status'] == config('constants.PENDING') ? 'warning' : ($order['status'] == config('constants.PROCESSING') ? 'info' : 'success') }}">
                                {{ $order['status_text'] }}
                            </span>
                        </td>
                        <td>EGP {{ number_format($order['total'], 2) }}</td>
                        <td>{{ $order['created_at'] }}</td>
                        <td>
                            @if($order['status'] == config('constants.PENDING'))
                            <form method="POST" action="{{ route('warehouse.accept-order', $order['id']) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Accept</button>
                            </form>
                            <form method="POST" action="{{ route('warehouse.reject-order', $order['id']) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                            </form>
                            @elseif($order['status'] == config('constants.PROCESSING'))
                            <form method="POST" action="{{ route('warehouse.ready-for-delivery', $order['id']) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Ready for Delivery</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No recent orders found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Products -->
    @if($lowStockProducts->count() > 0)
    <div class="section-card">
        <h5 class="section-title">Low Stock Products</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts as $product)
                    <tr>
                        <td>{{ $product['name'] }}</td>
                        <td>{{ $product['sku'] }}</td>
                        <td>
                            <span class="badge bg-{{ $product['stock_quantity'] <= 2 ? 'danger' : 'warning' }}">
                                {{ $product['stock_quantity'] }} left
                            </span>
                        </td>
                        <td>EGP {{ number_format($product['normal_price'], 2) }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary">Restock</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif


    <!-- Watch Boxes -->
    <div class="section-card">
        <h5 class="section-title">Watch Boxes <span class="badge bg-primary">4,300 Total</span></h5>
        <div class="row g-3">
            <div class="col-md-2">
                <div class="mini-stat">
                    <div class="mini-stat-value">800</div>
                    <div class="mini-stat-label">Gift Box</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mini-stat">
                    <div class="mini-stat-value">500</div>
                    <div class="mini-stat-label">Luxury Box</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mini-stat">
                    <div class="mini-stat-value">500</div>
                    <div class="mini-stat-label">High Luxury Box</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mini-stat">
                    <div class="mini-stat-value">2,500</div>
                    <div class="mini-stat-label">Original Box</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mini-stat border border-warning">
                    <div class="mini-stat-value text-warning">600</div>
                    <div class="mini-stat-label">Needs Feeding</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mini-stat border border-success">
                    <div class="mini-stat-value text-success">0</div>
                    <div class="mini-stat-label">Over Stock</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shopping Bags -->
    <div class="section-card">
        <h5 class="section-title">Shopping Bags <span class="badge bg-primary">4,000 Total</span></h5>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="mini-stat">
                    <div class="mini-stat-value">2,500</div>
                    <div class="mini-stat-label">Watch Bags</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat">
                    <div class="mini-stat-value">2,000</div>
                    <div class="mini-stat-label">Accessories Bags</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat border border-warning">
                    <div class="mini-stat-value text-warning">500</div>
                    <div class="mini-stat-label">Needs Feeding</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat border border-success">
                    <div class="mini-stat-value text-success">0</div>
                    <div class="mini-stat-label">Over Stock</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prime Bags -->
    <div class="section-card">
        <h5 class="section-title">Prime Bags <span class="badge bg-primary">4,000 Total</span></h5>
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="mini-stat">
                    <div class="mini-stat-value">1,000</div>
                    <div class="mini-stat-label">Prime Small</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat">
                    <div class="mini-stat-value">500</div>
                    <div class="mini-stat-label">Prime Medium</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat">
                    <div class="mini-stat-value">1,500</div>
                    <div class="mini-stat-label">Prime Large</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat">
                    <div class="mini-stat-value">1,000</div>
                    <div class="mini-stat-label">Prime XLarge</div>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="mini-stat border border-warning">
                    <div class="mini-stat-value text-warning">500</div>
                    <div class="mini-stat-label">Prime Bags Needs Feeding</div>
                    <small class="d-block mt-2 text-muted">200 Small - 100 Medium - 150 Large - 50 XLarge</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mini-stat border border-success">
                    <div class="mini-stat-value text-success">0</div>
                    <div class="mini-stat-label">Bags Over Stock</div>
                    <small class="d-block mt-2 text-muted">0 Small - 0 Medium - 0 Large - 0 XLarge</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Damaged Goods -->
    <div class="section-card">
        <h5 class="section-title">Damaged Goods <span class="badge bg-danger">327 Total - 150K EGP</span></h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="fas fa-clock text-primary me-2"></i>Watches</td>
                        <td><span class="badge bg-danger">120 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-mobile-alt text-primary me-2"></i>Smart Watches</td>
                        <td><span class="badge bg-danger">20 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-wallet text-primary me-2"></i>Wallets</td>
                        <td><span class="badge bg-danger">50 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-belt text-primary me-2"></i>Belts</td>
                        <td><span class="badge bg-danger">30 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-shopping-bag text-primary me-2"></i>Bags</td>
                        <td><span class="badge bg-danger">30 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-gem text-primary me-2"></i>Bracelet</td>
                        <td><span class="badge bg-success">0 Pieces</span></td>
                        <td><span class="text-success">None</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-hat-cowboy text-primary me-2"></i>Caps</td>
                        <td><span class="badge bg-danger">25 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-hat-wizard text-primary me-2"></i>Hats</td>
                        <td><span class="badge bg-danger">15 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-scarf text-primary me-2"></i>Scarves</td>
                        <td><span class="badge bg-danger">10 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-box text-primary me-2"></i>Multi Watch Box</td>
                        <td><span class="badge bg-danger">5 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-clock text-primary me-2"></i>Wall Clocks</td>
                        <td><span class="badge bg-danger">2 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-spray-can text-primary me-2"></i>Perfumes</td>
                        <td><span class="badge bg-danger">20 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-ellipsis-h text-primary me-2"></i>Other</td>
                        <td><span class="badge bg-danger">10 Pieces</span></td>
                        <td><span class="text-danger">Damaged</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Damaged Materials -->
    <div class="section-card">
        <h5 class="section-title">Damaged Boxes and Shopping Bags <span class="badge bg-danger">280 Total - 13K EGP</span></h5>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">Shopping Bags</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Watch Bags</span>
                            <strong>50 Pieces</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Accessories Bags</span>
                            <strong>80 Pieces</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">Prime Bags</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Small</span>
                            <strong>10 Pieces</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Medium</span>
                            <strong>70 Pieces</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Large</span>
                            <strong>30 Pieces</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>XLarge</span>
                            <strong>40 Pieces</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">Watch Boxes</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Gift Box</span>
                            <strong>10 Pieces</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Luxury Box</span>
                            <strong>70 Pieces</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>High Luxury Box</span>
                            <strong>30 Pieces</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Original Box</span>
                            <strong>40 Pieces</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart data from Laravel
    // const chartData = {
    //     !!json_encode($chartData ?? ['labels' => [], 'data' => [], 'values' => []]) !!
    // };
    const chartData = {
        !!json_encode($chartData ?? ['labels' => [], 'data' => [], 'values' => []]) !!
    };

    console.log('Chart Data:', chartData);

    // Prepare data for Chart.js
    const labels = chartData.labels || [];
    const data = chartData.data || [];
    const values = chartData.values || [];

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Create the chart
        const ctx = document.getElementById('sendOutChart');
        console.log('Canvas element found:', ctx);

        if (ctx) {
            console.log('Creating chart...');
            const sendOutChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Orders Count',
                        data: data,
                        borderColor: 'rgb(13, 110, 253)',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        yAxisID: 'y',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2
                    }, {
                        label: 'Total Value (EGP)',
                        data: values,
                        borderColor: 'rgb(25, 135, 84)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        yAxisID: 'y1',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Number of Orders',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Total Value (EGP)',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Warehouse Performance - Send Out Items (Last 30 Days)',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        if (context.dataset.yAxisID === 'y1') {
                                            label += 'EGP ' + context.parsed.y.toLocaleString();
                                        } else {
                                            label += context.parsed.y + ' orders';
                                        }
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            console.error('Canvas element not found!');
        }

        // Handle period selection
        document.getElementById('performancePeriod').addEventListener('change', function() {
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');

            if (this.value === 'custom') {
                dateFrom.style.display = 'block';
                dateTo.style.display = 'block';
            } else {
                dateFrom.style.display = 'none';
                dateTo.style.display = 'none';

                // Reload page with selected period
                if (this.value !== 'custom') {
                    window.location.href = '{{ route("warehouse.home") }}?period=' + this.value;
                }
            }
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

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .bg-primary {
        background-color: #0d6efd !important;
    }

    .bg-success {
        background-color: #198754 !important;
    }

    .bg-warning {
        background-color: #ffc107 !important;
    }

    .bg-info {
        background-color: #0dcaf0 !important;
    }

    .rounded-circle {
        border: 2px solid #dee2e6;
    }

    .position-relative .badge {
        font-size: 0.6em;
    }

    #sendOutChart {
        max-height: 350px;
    }

    .form-select-sm,
    .form-control-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1) !important;
    }

    .card-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.125);
    }

    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }

    .rounded-circle {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection
