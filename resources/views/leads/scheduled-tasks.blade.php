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

<!-- Scheduled Tasks Page -->
<div id="scheduled-tasks">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Scheduled Tasks</h2>
        <div class="d-flex">
            <form action="{{route('leads.create-task')}}" method="GET">
                @csrf
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-plus me-1"></i> Create Task
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
                <a href="{{ route('import-export.template', ['type' => 'scheduled_tasks']) }}" class="btn btn-warning">
                    <i class="fas fa-file-download me-1"></i> Template
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="card-title text-danger">Hot Leads Tasks</h6>
                    <h2 class="mb-0 text-danger">{{$hot_leads_count}}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="card-title text-warning">Warm Leads Tasks</h6>
                    <h2 class="mb-0 text-warning">{{$warm_leads_count}}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="card-title text-primary">Today's Tasks</h6>
                    <h2 class="mb-0 text-primary">{{$today_tasks_count}}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="card-title text-success">Tomorrow's Tasks</h6>
                    <h2 class="mb-0 text-success">{{$tomorrow_tasks_count}}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="stripe row-border order-column" id="tasks-list">
                    <thead>
                        <tr>
                            <th>Lead Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Source</th>
                            <th>Interests</th>
                            <th>Potential</th>
                            <th>Last Follow Up</th>
                            <th>Task Day</th>
                            <th>Interest</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($formated_tasks as $task)
                        <tr>
                            <td>{{$task["lead"]["name"]}}</td>
                            <td>{{$task["lead"]["phone_numbers"]}}</td>
                            <td>{{$task["lead"]["email"]}}</td>
                            <td>
                                @if($task["lead"]['source'] === "Whatsapp")
                                <span class="lead-source-icon source-whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                </span>
                                WhatsApp
                                @elseif($task["lead"]['source'] === "Phone")
                                <span class="lead-source-icon source-phone">
                                    <i class="fas fa-phone"></i>
                                </span>
                                Phone
                                @elseif($task["lead"]['source'] === "Facebook")
                                <span class="lead-source-icon source-facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </span>
                                Facebook
                                @elseif($task["lead"]['source'] === "Instagram")
                                <span class="lead-source-icon source-instagram">
                                    <i class="fab fa-instagram"></i>
                                </span>
                                Instagram
                                @else
                                {{$task["lead"]['source']}}
                                @endif
                            </td>
                            <td>
                                <span class="tag">{{$task["lead"]["interested_categories"]}}</span>
                                <span class="tag">{{$task["lead"]["interested_products_skus"]}}</span>
                            </td>
                            <td>EGP {{$task["lead"]["potential"]}}</td>
                            <td>
                                @if($task["task_done"]==="1")
                                {{$task["complete_date"]}}
                                @else
                                {{$task["task_date"]}}
                                @endif
                            </td>
                            <td>
                                @if($task["task_date"]===today())
                                Today
                                @elseif($task["task_date"]===today()->addDay())
                                Tomorrow
                                @else
                                {{$task["task_date"]}}
                                @endif
                            </td>
                            <td><span class="badge interest-hot"> {{$task["lead"]["degree_of_interest"]}}</span></td>
                            <td>Lead</td>
                            <td>
                                <button class="btn btn-sm btn-outline-success me-1" title="Call">
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="Chat">
                                    <i class="fas fa-comment"></i>
                                </button>
                                @if($task["task_done"] === "0")
                                <form action="{{route('leads.task-done')}}" method="POST">
                                    @csrf
                                    <input type="hidden" name="task_id" value="{{$task['id']}}" />
                                    <button class="btn btn-sm btn-outline-primary" title="Mark Done">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        <tr>
                            <td>Karim Adel</td>
                            <td>+20 100 123 4567</td>
                            <td>karim@example.com</td>
                            <td>
                                <span class="lead-source-icon source-whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                </span>
                                WhatsApp
                            </td>
                            <td>
                                <span class="tag">Watches</span>
                                <span class="tag">SKU-123</span>
                            </td>
                            <td>EGP 1,200</td>
                            <td>
                                15 May 2023
                                <button class="btn btn-sm btn-link p-0 ms-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                            <td>Today</td>
                            <td><span class="badge interest-hot">Hot</span></td>
                            <td>Lead</td>
                            <td>
                                <button class="btn btn-sm btn-outline-success me-1" title="Call">
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="Chat">
                                    <i class="fas fa-comment"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Nada Walid</td>
                            <td>+20 101 234 5678</td>
                            <td>nada@example.com</td>
                            <td>
                                <span class="lead-source-icon source-facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </span>
                                Facebook
                            </td>
                            <td>
                                <span class="tag">Bags</span>
                                <span class="tag">SKU-456</span>
                            </td>
                            <td>EGP 850</td>
                            <td>
                                10 May 2023
                                <button class="btn btn-sm btn-link p-0 ms-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                            <td>Today</td>
                            <td><span class="badge interest-warm">Warm</span></td>
                            <td>Lead</td>
                            <td>
                                <button class="btn btn-sm btn-outline-success me-1" title="Call">
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="Chat">
                                    <i class="fas fa-comment"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Ahmed Samy</td>
                            <td>+20 104 567 8901</td>
                            <td>ahmed@example.com</td>
                            <td>
                                <span class="lead-source-icon source-phone">
                                    <i class="fas fa-phone"></i>
                                </span>
                                Phone
                            </td>
                            <td>
                                <span class="tag">Watches</span>
                                <span class="tag">SKU-101</span>
                            </td>
                            <td>EGP 1,500</td>
                            <td>
                                8 May 2023
                                <button class="btn btn-sm btn-link p-0 ms-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                            <td>Tomorrow</td>
                            <td><span class="badge interest-warm">Warm</span></td>
                            <td>Lead</td>
                            <td>
                                <button class="btn btn-sm btn-outline-success me-1" title="Call">
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="Chat">
                                    <i class="fas fa-comment"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Laila Kamal</td>
                            <td>+20 103 456 7890</td>
                            <td>laila@example.com</td>
                            <td>
                                <span class="lead-source-icon source-other">
                                    <i class="fas fa-question"></i>
                                </span>
                                Other
                            </td>
                            <td>
                                <span class="tag">Caps</span>
                                <span class="tag">SKU-202</span>
                            </td>
                            <td>EGP 450</td>
                            <td>
                                -
                            </td>
                            <td>Tomorrow</td>
                            <td><span class="badge interest-cold">Cold</span></td>
                            <td>Lead</td>
                            <td>
                                <button class="btn btn-sm btn-outline-success me-1" title="Call">
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="Chat">
                                    <i class="fas fa-comment"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Lead Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Source</th>
                            <th>Interests</th>
                            <th>Potential</th>
                            <th>Last Follow Up</th>
                            <th>Task Day</th>
                            <th>Interest</th>
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
<script>
    $(document).ready(function() {
        let table = new DataTable('#tasks-list', {
            initComplete: function() {
                // Setup - add a text input to each footer cell
                $('#tasks-list tfoot th').each(function(i) {
                    var title = $('#tasks-list tfoot th')
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
                <h5 class="modal-title" id="exportModalLabel">Export Scheduled Tasks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.export') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="scheduled_tasks">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_format" class="form-label">Export Format</label>
                        <select class="form-select" id="export_format" name="format">
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_user" class="form-label">User Filter</label>
                        <input type="text" class="form-control" id="export_user" name="user_id" placeholder="User ID">
                    </div>
                    <div class="mb-3">
                        <label for="export_done" class="form-label">Task Status Filter</label>
                        <select class="form-select" id="export_done" name="task_done">
                            <option value="">All Statuses</option>
                            <option value="0">Pending</option>
                            <option value="1">Completed</option>
                        </select>
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
                <h5 class="modal-title" id="importModalLabel">Import Scheduled Tasks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="scheduled_tasks">
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
