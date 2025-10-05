@extends("layouts.main")

@section("content")
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
<!-- Orders List Page -->
<div id="orders-list">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Orders List</h2>
        <div class="d-flex">
            <form method="GET" action="{{route('orders.create')}}">
                @csrf
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-plus me-1"></i> Add Order
                </button>
            </form>
            <div class="btn-group" role="group">
                <!-- Export Button -->
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="fas fa-download me-1"></i> Export
                </button>
                <!-- Import Button -->
                <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-upload me-1"></i> Import
                </button>
                <!-- Template Button -->
                <a href="{{ route('import-export.template', ['type' => 'orders']) }}" class="btn btn-warning">
                    <i class="fas fa-file-download me-1"></i> Template
                </a>
            </div>
        </div>
    </div>

    {{-- <div class="card mb-4">--}}
    {{-- <div class="card-body">--}}
    {{-- <div class="row">--}}
    {{-- <div class="col-md-6">--}}
    {{-- <div class="input-group mb-3">--}}
    {{-- <input type="text" class="form-control" placeholder="Search by order # or phone">--}}
    {{-- <button class="btn btn-outline-secondary" type="button">--}}
    {{-- <i class="fas fa-search"></i>--}}
    {{-- </button>--}}
    {{-- </div>--}}
    {{-- </div>--}}
    {{-- <div class="col-md-6">--}}
    {{-- <div class="d-flex">--}}
    {{-- <select class="form-select me-2">--}}
    {{-- <option selected>Order Status</option>--}}
    {{-- <option>Delivered</option>--}}
    {{-- <option>Pending Warehouse</option>--}}
    {{-- <option>Warehouse Accepted</option>--}}
    {{-- <option>Out for Delivery</option>--}}
    {{-- <option>Cancelled</option>--}}
    {{-- <option>Returned</option>--}}
    {{-- </select>--}}
    {{-- <select class="form-select me-2">--}}
    {{-- <option selected>Governorate</option>--}}
    {{-- <option>Cairo</option>--}}
    {{-- <option>Alexandria</option>--}}
    {{-- <option>Giza</option>--}}
    {{-- <option>Aswan</option>--}}
    {{-- <option>Red Sea</option>--}}
    {{-- </select>--}}
    {{-- <select class="form-select">--}}
    {{-- <option selected>Delivery Agent</option>--}}
    {{-- <option>Mohamed Fathy</option>--}}
    {{-- <option>Mostafa Hanafi</option>--}}
    {{-- <option>Youssef</option>--}}
    {{-- <option>Mostafa Othman</option>--}}
    {{-- </select>--}}
    {{-- </div>--}}
    {{-- </div>--}}
    {{-- </div>--}}
    {{-- </div>--}}
    {{-- </div>--}}

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="stripe row-border order-column" id="order-list">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Commission</th>
                            <th>Governorate</th>
                            <th>Delivery Agent</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($formated_orders as $order)
                        <tr>
                            <td>#{{$order['order_number']}}</td>
                            <td><a href="#" class="text-primary">{{$order['customer']['name']}}</a></td>
                            <td>
                                @if($order['status'] === config("constants.PENDING"))
                                <span class="status-badge badge-pending">Pending Warehouse Acceptance</span>
                                @elseif($order['status'] === config("constants.PROCESSING"))
                                <span class="status-badge badge-processing">Moving Manager Processing</span>
                                @elseif($order['status'] === config("constants.OUT_FOR_DELIVERY"))
                                <span class="status-badge badge-delivering">Out For Delivery</span>
                                @elseif($order['status'] === config("constants.DELIVERED"))
                                <span class="status-badge badge-delivered">Delivered</span>
                                @elseif($order['status'] === config("constants.CANCELLED"))
                                <span class="status-badge badge-cancelled">Cancelled</span>
                                @elseif($order['status'] === config("constants.RETURNED"))
                                <span class="status-badge badge-returned">Returned To Warehouse</span>
                                @else
                                <span class="status-badge badge-accepted">Warehouse Accepted</span>
                                @endif
                            </td>
                            <td>EGP {{$order['total']}}</td>
                            <td>EGP {{$order['employee_commission']}}</td>
                            <td>{{$order['governorate']}}</td>
                            @if($order['status'] === config("constants.OUT_FOR_DELIVERY"))
                            <td>{{$order['delivery_agent']['name']}}</td>
                            @else
                            <td>-</td>
                            @endif
                            <td>{{$order['employee']['name']}}</td>
                            <td>{{$order['created_at']}}</td>
                            <td>{{$order['updated_at']}}</td>
                            <td>
                                @if($order['status'] === config("constants.PENDING"))
                                @if(auth()->user()->hasPermission('orders', 'edit'))
                                <a href="{{route('orders.edit', ["id"=>$order['id']])}}" role="button" class="btn btn-sm btn-outline-secondary me-1" title="Edit Order">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @endif
                                <button class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Commission</th>
                            <th>Governorate</th>
                            <th>Delivery Agent</th>
                            <th>Created By</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section("scripts")
<script>
    $(document).ready(function() {
        let table = new DataTable('#order-list', {
            initComplete: function() {
                // Setup - add a text input to each footer cell
                $('#order-list tfoot th').each(function(i) {
                    var title = $('#order-list tfoot th')
                        .eq($(this).index())
                        .text();
                    $(this).html(
                        '<input type="text" placeholder="' + title + '" data-index="' + i + '" />'
                    );

                });

            },
            fixedHeader: {
                footer: true
            }
        });

        // Filter event handler
        $(table.table().container()).on('keyup', 'tfoot input', function() {
            table
                .column($(this).data('index'))
                .search(this.value)
                .draw();
        });
    });
</script>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.export') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="orders">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_format" class="form-label">Export Format</label>
                        <select class="form-select" id="export_format" name="format">
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_status" class="form-label">Status Filter</label>
                        <select class="form-select" id="export_status" name="status">
                            <option value="">All Statuses</option>
                            <option value="1">Pending</option>
                            <option value="2">Processing</option>
                            <option value="3">Out for Delivery</option>
                            <option value="4">Delivered</option>
                            <option value="5">Cancelled</option>
                            <option value="6">Returned</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_employee" class="form-label">Employee Filter</label>
                        <input type="text" class="form-control" id="export_employee" name="employee_id" placeholder="Employee ID">
                    </div>
                    <div class="mb-3">
                        <label for="export_governorate" class="form-label">Governorate Filter</label>
                        <input type="text" class="form-control" id="export_governorate" name="governorate" placeholder="Governorate">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="export_date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="export_date_from" name="date_from">
                        </div>
                        <div class="col-md-6">
                            <label for="export_date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="export_date_to" name="date_to">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="orders">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Select File</label>
                        <input type="file" class="form-control" id="import_file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Supported formats: Excel (.xlsx, .xls), CSV (.csv). Max size: 10MB</div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Make sure your file follows the template format. Download the template first if you're unsure.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
