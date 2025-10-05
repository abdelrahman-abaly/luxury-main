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

    <!-- My Wallet Page -->
    <div id="wallet">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">My Wallet</h2>
            <div>Date Today : {{date("l, F j, Y")}} {{date('n') . "    " . date('Y')}}</div>
            @if(auth()->user()->hasPermission('employee_salary', 'create'))
                <form action="{{route('my-wallet.salary-calc')}}" method="GET">
                    @csrf
                    <button type="submit" class="btn btn btn-primary">
                        <i class="fa-solid fa-dollar-sign"></i>alary Calc
                    </button>
                </form>
            @endif
{{--            <div class="d-flex">--}}
{{--                <select class="form-select me-2">--}}
{{--                    <option selected>All Transactions</option>--}}
{{--                    <option>Salary</option>--}}
{{--                    <option>Commission</option>--}}
{{--                    <option>Borrowing</option>--}}
{{--                </select>--}}
{{--                <button class="btn btn-outline-secondary">--}}
{{--                    <i class="fas fa-download me-1"></i> Export--}}
{{--                </button>--}}
{{--            </div>--}}
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="wallet-card bg-success bg-opacity-10">
                    <i class="fas fa-wallet fa-2x text-success"></i>
                    <div class="label">Balance Received</div>
                    <div class="amount">EGP {{number_format($balance_received,2)}}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wallet-card bg-primary bg-opacity-10">
                    <i class="fas fa-clock fa-2x text-primary"></i>
                    <div class="label">Balance Pending</div>
                    <div class="amount">EGP {{number_format($balance_pending,2)}}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wallet-card bg-danger bg-opacity-10">
                    <i class="fas fa-hand-holding-usd fa-2x text-danger"></i>
                    <div class="label">Borrowed Balance</div>
                    <div class="amount">EGP {{number_format($borrowed_balance,2)}}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Salary Wallet</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Days Worked</th>
                                        <th>Salary Wallet</th>
                                        <th>Borrowing Balance</th>
                                        <th>Ready Salary</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($salary_data as $data)
                                    <tr>
                                        <td>{{$data->id}}</td>
                                        <td>{{$data->days_worked}}</td>
                                        <td>{{number_format($data->salary_wallet)}} EGP</td>
                                        <td>{{number_format($data->borrowing_balance)}} EGP</td>
                                        <td>{{number_format($data->ready_salary)}} EGP</td>
                                        <td>{{date('F', mktime(0, 0, 0, $data->month, 1))}}</td>
                                        <td>{{$data->year}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('my-wallet.submit-borrow-request')}}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <input type="hidden" name="user_id" value="{{auth()->user()->user_id}}">
                                <label class="form-label">Borrowing Balance This Month: <strong>EGP {{number_format($borrowed_balance)}}</strong></label>
                                <div class="input-group">
                                    <input type="number" name="amount" class="form-control" placeholder="Enter amount">
                                    <button type="submit" class="btn btn-outline-primary">Request Borrowing</button>
                                </div>
                                <small>Maximum borrowing limit: EGP 10,000</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Commission Wallet</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-3">
                                <div class="p-1 rounded">
                                    <h6 class="mb-0">On Processing</h6>
                                    <h6 class="mb-0">EGP {{number_format($onProcessingTotalCommission)}}</h6>
                                    <small>{{number_format($onProcessingOrdersCount)}} orders</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-1 rounded">
                                    <h6 class="mb-0">Pending</h6>
                                    <h6 class="mb-0">EGP {{number_format($pendingTotalCommission)}}</h6>
                                    <small>{{number_format($pendingOrdersCount)}} orders</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-1 rounded">
                                    <h6 class="mb-0">Ready To Pay</h6>
                                    <h6 class="mb-0">EGP {{number_format($readyToPayTotalCommission)}}</h6>
                                    <small>{{number_format($readyToPayOrdersCount)}} orders</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-1 rounded">
                                    <h6 class="mb-0">Completed</h6>
                                    <h6 class="mb-0">EGP {{number_format($completedTotalCommission)}}</h6>
                                    <small>{{number_format($completedOrdersCount)}} orders</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <form action="{{route('my-wallet.submit-commission-withdrawal-request')}}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{auth()->user()->user_id}}">
                                <input type="hidden" name="balance" value="{{number_format($readyToPayTotalCommission)}}">
                                <label class="form-label">Withdrawal Request</label>
                                <div class="input-group">
                                    <input type="number" name="amount" class="form-control" placeholder="Enter amount">
                                    <button class="btn btn-outline-primary">Request Withdrawal</button>
                                </div>
                                <small>Available balance: EGP {{number_format($readyToPayTotalCommission)}} (Ready To Pay)</small>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Transaction History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="stripe row-border order-column" id="transactionHistoryTable">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Type</th>
                                <th>Send Date</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>New Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $item)
                            <tr>
                                <td>{{$item->transaction_id}}</td>
                                @if($item->type === config("constants.Commission Request"))
                                    <td><span class="badge bg-primary">Commission Request</span></td>
                                @elseif($item->type === config("constants.Borrowing Request"))
                                    <td><span class="badge bg-info">Borrowing Request</span></td>
                                @else
                                    <td><span class="badge bg-success">Salary Payment</span></td>
                                @endif

                                <td>{{$item->send_date}}</td>
                                @if($item->status === config("constants.Paid"))
                                    <td><span class="badge bg-success">Paid</span></td>
                                @elseif($item->status === config("constants.Pending Approval"))
                                    <td><span class="badge bg-info">Pending Approval</span></td>
                                @else
                                    <td><span class="badge bg-danger">Rejected</span></td>
                                @endif

                                <td>
                                    @if($item->status === config("constants.Pending Approval"))
                                        <form action="{{route('my-wallet.approve-request')}}" method="POST" class="mb-2">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$item->transaction_id}}" />
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-circle-fill"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{route('my-wallet.reject-request')}}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$item->transaction_id}}" />
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-circle-fill"></i> Reject
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td>EGP {{$item->amount}}</td>
                                <td>EGP {{$item->balance}}</td>
                                <td>EGP {{$item->new_balance}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Type</th>
                                <th>Send Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>New Balance</th>
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
            let table = new DataTable('#transactionHistoryTable', {
                initComplete: function () {
                    // Setup - add a text input to each footer cell
                    $('#transactionHistoryTable tfoot th').each(function (i) {
                        var title = $('#transactionHistoryTable tfoot th')
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
            $(table.table().container()).on('keyup', 'tfoot input', function () {
                table
                    .column($(this).data('index'))
                    .search(this.value)
                    .draw();
            });
        });
    </script>
@endsection
