@extends("layouts.main")

@section("content")
<!-- Home Content -->
<div id="home">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 dashboard-header">Dashboard Overview</h2>
        {{-- <div class="d-flex">--}}
        {{-- <input type="date" class="form-control me-2" id="dateFrom">--}}
        {{-- <input type="date" class="form-control" id="dateTo">--}}
        {{-- </div>--}}
    </div>

    <div class="row">
        <!-- Performance Card -->
        @php
        $performance_class = "";
        if($performance==="User") {
        $performance_class = "level-user";
        } else if ($performance==="Beginner") {
        $performance_class = "level-beginner";
        } else if ($performance==="Rising") {
        $performance_class = "level-rising";
        } else if ($performance==="Expert") {
        $performance_class = "level-expert";
        } else if ($performance==="Pioneer") {
        $performance_class = "level-pioneer";
        } else {
        $performance_class = "level-professional";
        }
        @endphp
        <div class="col-md-4">
            <div class="card {{$performance_class}}">
                <div class="card-body text-center">
                    <h5 class="card-title">Performance Level</h5>
                    <div class="my-3">
                        <span class="badge bg-white text-dark fs-5 p-2">{{$performance}}</span>
                    </div>
                    <p class="card-text">Commission Rate: {{$commission_rate}}</p>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{$progress}}%;" aria-valuenow="{{$progress}}" aria-valuemin="0" aria-valuemax="100">{{$delivered_orders}}/{{$total}}</div>
                    </div>
                    <p class="mb-0">Next level: {{$next_level}} ({{$next_commission_rate}})</p>
                </div>
            </div>
        </div>

        <!-- My Orders Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">My Orders</h5>
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-2 bg-success bg-opacity-10 rounded">
                                <h6 class="mb-0">Delivered</h6>
                                <h4 class="mb-0 text-success">{{$delivered_orders}}</h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-2 bg-warning bg-opacity-10 rounded">
                                <h6 class="mb-0">Pending</h6>
                                <h4 class="mb-0 text-warning">{{$pending_orders}}</h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-2 bg-primary bg-opacity-10 rounded">
                                <h6 class="mb-0">Processing</h6>
                                <h4 class="mb-0 text-primary">{{$processing_orders}}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-info bg-opacity-10 rounded">
                                <h6 class="mb-0">Out for Delivery</h6>
                                <h4 class="mb-0 text-info">{{$out_for_delivery_orders}}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-danger bg-opacity-10 rounded">
                                <h6 class="mb-0">Cancelled</h6>
                                <h4 class="mb-0 text-danger">{{$cancelled_orders}}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-secondary bg-opacity-10 rounded">
                                <h6 class="mb-0">Returned</h6>
                                <h4 class="mb-0 text-secondary">{{$returned_orders}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leads Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Leads Overview</h5>
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-2 bg-danger bg-opacity-10 rounded">
                                <h6 class="mb-0">Total Leads</h6>
                                <h4 class="mb-0">{{$total_leads}}</h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-2 bg-primary bg-opacity-10 rounded">
                                <h6 class="mb-0">Scheduled Tasks</h6>
                                <h4 class="mb-0">{{$scheduled_tasks}}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-warning bg-opacity-10 rounded">
                                <h6 class="mb-0">Cancelled Leads</h6>
                                <h4 class="mb-0">{{$cancelled_leads}}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-success bg-opacity-10 rounded">
                                <h6 class="mb-0">Conversion Rate</h6>
                                <h4 class="mb-0">{{$conversion_rate}}%</h4>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="mb-1">Potential Commission: <strong>EGP {{number_format($potential_commission, 0)}}</strong></p>
                        <div class="progress" style="height: 8px;">
                            @php
                            $max_potential = 10000; // Maximum potential for progress bar calculation
                            $progress_percentage = min(($potential_commission / $max_potential) * 100, 100);
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{$progress_percentage}}%;" aria-valuenow="{{$progress_percentage}}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Orders</h5>
            <a href="{{route('orders.list')}}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Commission</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($formattedOrders as $order)
                        <tr>
                            <td>#{{$order['order_number']}}</td>
                            <td>{{$order['customer']}}</td>
                            <td>
                                @if($order['status']===config("constants.DELIVERED"))
                                <span class="status-badge badge-delivered">Delivered</span>
                                @elseif($order['status']===config("constants.PENDING"))
                                <span class="status-badge badge-pending">Pending</span>
                                @elseif($order['status']===config("constants.PROCESSING"))
                                <span class="status-badge badge-processing">Processing</span>
                                @elseif($order['status']===config("constants.OUT_FOR_DELIVERY"))
                                <span class="status-badge badge-delivering">Out For Delivery</span>
                                @elseif($order['status']===config("constants.CANCELLED"))
                                <span class="status-badge badge-cancelled">Cancelled</span>
                                @elseif($order['status']===config("constants.RETURNED"))
                                <span class="status-badge badge-returned">Returned</span>
                                @elseif($order['status']===config("constants.ACCEPTED"))
                                <span class="status-badge badge-accepted">Accepted</span>
                                @endif

                            </td>
                            <td>EGP {{$order['total']}}</td>
                            <td>EGP {{$order['employee_commission']}}</td>
                            <td>{{$order['created_at']}}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Leads -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Leads</h5>
            <a href="#leads-list" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Interests</th>
                            <th>Potential</th>
                            <th>Interest</th>
                            <th>Next Follow Up</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_leads as $lead)
                        <tr>
                            <td>{{$lead->name}}</td>
                            <td>{{$lead->phone_numbers}}</td>
                            <td>
                                <span class="tag">{{$lead->interested_categories}}</span>
                                <span class="tag">SKU-{{$lead->interested_products_skus}}</span>
                            </td>
                            <td>{{$lead->potential}}</td>
                            <td>
                                @if($lead->degree_of_interest==="Hot")
                                <span class="badge interest-hot">Hot</span>
                                @elseif($lead->degree_of_interest==="Warm")
                                <span class="badge interest-warm">Warm</span>
                                @elseif($lead->degree_of_interest==="Cold")
                                <span class="badge interest-cold">Cold</span>
                                @else
                                <span class="badge interest-cancelled">Cancelled</span>
                                @endif
                            </td>
                            <td>{{$lead->next_follow_up_period}}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-success me-1"><i class="fas fa-phone"></i></button>
                                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-comment"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
