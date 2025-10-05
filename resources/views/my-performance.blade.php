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

    <!-- My Performance Page -->
    <div id="performance">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">My Performance</h2>
{{--            <div class="d-flex">--}}
{{--                <button class="btn btn-outline-secondary">--}}
{{--                    <i class="fas fa-download me-1"></i> Export--}}
{{--                </button>--}}
{{--            </div>--}}
        </div>

        <div class="card mb-4 level-beginner">
            <div class="card-body text-center">
                <h3 class="card-title">Current Performance Level</h3>
                <div class="my-4">
                    <span class="badge bg-white text-dark fs-4 p-3">{{$performance}}</span>
                </div>
                <p class="card-text fs-5">Commission Rate: {{$commission_rate}}</p>
                <div class="progress mb-4" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{$progress}}%;" aria-valuenow="{{$progress}}" aria-valuemin="0" aria-valuemax="100">
                        <strong>{{$delivered_orders}}/{{$total}} Orders</strong>
                    </div>
                </div>
                <p class="mb-0 fs-5">Next level: {{$next_level}} ({{$next_commission_rate}})</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Performance Progress</h5>
                        <div style="min-height: 300px;">
                            <!-- This would be a chart in a real implementation -->
                            <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 100%;">
                                <div class="card-body">
                                    <canvas id="monthlyOrdersChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Level Requirements</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Level</th>
                                    <th>Orders Required</th>
                                    <th>Commission Rate</th>
                                    <th>Your Progress</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>User</td>
                                    <td>0-99</td>
                                    <td>0%</td>
                                    <td>
                                        <div class="progress progress-level">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{$user_progress}}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Beginner</td>
                                    <td>100-199</td>
                                    <td>1%</td>
                                    <td>
                                        <div class="progress progress-level">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{$beginner_progress}}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Rising</td>
                                    <td>200-299</td>
                                    <td>2%</td>
                                    <td>
                                        <div class="progress progress-level">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{$rising_progress}}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Expert</td>
                                    <td>300-399</td>
                                    <td>3%</td>
                                    <td>
                                        <div class="progress progress-level">
                                            <div class="progress-bar" role="progressbar" style="width: {{$expert_progress}}%; background-color: #6c757d;"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Pioneer</td>
                                    <td>400-499</td>
                                    <td>4%</td>
                                    <td>
                                        <div class="progress progress-level">
                                            <div class="progress-bar" role="progressbar" style="width: {{$pioneer_progress}}%; background-color: #6c757d;"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Professional</td>
                                    <td>500+</td>
                                    <td>5%</td>
                                    <td>
                                        <div class="progress progress-level">
                                            <div class="progress-bar" role="progressbar" style="width: {{$professional_progress}}%; background-color: #6c757d;"></div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Monthly Performance History</h5>

                <div class="table-responsive">
                    <table class="stripe row-border order-column" id="performance-list">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Level Achieved</th>
                                    <th>Orders Count</th>
                                    <th>Commission Paid</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        <tbody>
                            @foreach($performance_history as $history)
                                <tr>
                                    <td>{{$history->month}} {{$history->year}}</td>
                                    @switch($history->level)
                                        @case("User")
                                            <td><span class="badge" style="background-color: #0a3a7a;">User</span></td>
                                            @break
                                        @case("Beginner")
                                            <td><span class="badge" style="background-color: #813dd5;">Beginner</span></td>
                                            @break
                                        @case("Rising")
                                            <td><span class="badge bg-primary">Rising</span></td>
                                            @break
                                        @case("Expert")
                                            <td><span class="badge bg-warning">Expert</span></td>
                                            @break
                                        @case("Pioneer")
                                            <td><span class="badge bg-info">Pioneer</span></td>
                                            @break
                                        @case("Professional")
                                            <td><span class="badge bg-danger">Professional</span></td>
                                            @break
                                    @endswitch
                                    <td>{{number_format($history->orders_count)}}</td>
                                    <td>EGP {{number_format($history->commission_amount)}}</td>
                                    @switch($history->status)
                                        @case("Paid")
                                            <td><span class="badge bg-success">Paid</span></td>
                                        @break
                                        @case("Processing")
                                            <td><span class="badge bg-warning">Processing</span></td>
                                        @break
                                    @endswitch
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Month</th>
                                <th>Level Achieved</th>
                                <th>Orders Count</th>
                                <th>Commission Paid</th>
                                <th>Status</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("scripts")
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            let table = new DataTable('#performance-list', {
                initComplete: function () {
                    // Setup - add a text input to each footer cell
                    $('#performance-list tfoot th').each(function (i) {
                        var title = $('#performance-list tfoot th')
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

            // Monthly Orders Line Chart
            new Chart(document.getElementById('monthlyOrdersChart'), {
                type: 'line',
                data: {
                    labels: @json($performanceData['monthly_orders']['labels']),
                    datasets: [{
                        label: 'Orders Completed',
                        data: @json($performanceData['monthly_orders']['data']),
                        backgroundColor: 'rgba(58, 123, 213, 0.2)',
                        borderColor: 'rgba(58, 123, 213, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Orders'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
