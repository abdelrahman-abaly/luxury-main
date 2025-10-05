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

<!-- Leads List Page -->
<div id="leads-list">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">My Leads List</h2>
        <div class="d-flex">
            <form action="{{route('leads.create')}}" method="GET">
                @csrf
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-plus me-1"></i> Add Lead
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
                <a href="{{ route('import-export.template', ['type' => 'leads']) }}" class="btn btn-warning">
                    <i class="fas fa-file-download me-1"></i> Template
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="card-title text-danger">Hot Leads</h6>
                    <h2 class="mb-0 text-danger">{{$hot_leads_count}}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="card-title text-warning">Warm Leads</h6>
                    <h2 class="mb-0 text-warning">{{$warm_leads_count}}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="card-title text-primary">Cold Leads</h6>
                    <h2 class="mb-0 text-primary">{{$cold_leads_count}}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="card-title text-success">Converted to Clients</h6>
                    <h2 class="mb-0 text-success">{{$converted_to_customers_count}}</h2>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="stripe row-border order-column" id="leads-list-table">
                    <thead>
                        <tr>
                            <th>Lead Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Source</th>
                            <th>Interests</th>
                            <th>Potential</th>
                            <th>Added By</th>
                            <th>Assigned To</th>
                            <th>Last Follow Up</th>
                            <th>Next Follow Up</th>
                            <th>Interest</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($formated_leads as $lead)
                        <tr>
                            <td>{{$lead['name']}}</td>
                            <td>{{$lead['phone_numbers']}}</td>
                            <td>{{$lead['email']}}</td>
                            <td>
                                @if($lead['source'] === "Whatsapp")
                                <span class="lead-source-icon source-whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                </span>
                                WhatsApp
                                @elseif($lead['source'] === "Phone")
                                <span class="lead-source-icon source-phone">
                                    <i class="fas fa-phone"></i>
                                </span>
                                Phone
                                @elseif($lead['source'] === "Facebook")
                                <span class="lead-source-icon source-facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </span>
                                Facebook
                                @elseif($lead['source'] === "Instagram")
                                <span class="lead-source-icon source-instagram">
                                    <i class="fab fa-instagram"></i>
                                </span>
                                Instagram
                                @else
                                {{$lead['source']}}
                                @endif
                            </td>
                            <td>
                                <span class="tag">{{$lead['interested_categories']}}</span>
                                <span class="tag">SKU-{{$lead['interested_products_skus']}}</span>
                            </td>
                            <td>EGP {{$lead['potential']}}</td>
                            <td>{{$lead['added_by']['name']}}</td>
                            <td>
                                {{$lead['assigned_to']['name']}}
                                <small class="d-block">{{$lead['days_remaining']}} days remaining</small>
                            </td>
                            <td>
                                {{$lead['updated_at']}}
                                <button class="btn btn-sm btn-link p-0 ms-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                            <td>{{$lead['next_follow_up_period']}}</td>
                            @if($lead['degree_of_interest'] === "Hot")
                            <td><span class="badge interest-hot">Hot</span></td>
                            @elseif($lead['degree_of_interest'] === "Cold")
                            <td><span class="badge interest-cold">Cold</span></td>
                            @elseif($lead['degree_of_interest'] === "Warm")
                            <td><span class="badge interest-warm">Warm</span></td>
                            @else
                            <td><span class="badge interest-cancelled">Cancelled</span></td>
                            @endif
                            @if($lead['is_customer'] === "0")
                            <td>Lead</td>
                            @else
                            <td>Customer</td>
                            @endif
                            <td>
                                <button class="btn btn-sm btn-outline-success me-1" title="Call">
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="Chat">
                                    <i class="fas fa-comment"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Lead Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Source</th>
                            <th>Interests</th>
                            <th>Potential</th>
                            <th>Added By</th>
                            <th>Assigned To</th>
                            <th></th>
                            <th></th>
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
        let table = new DataTable('#leads-list-table', {
            initComplete: function() {
                // Setup - add a text input to each footer cell
                $('#leads-list-table tfoot th').each(function(i) {
                    var title = $('#leads-list-table tfoot th')
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
                <h5 class="modal-title" id="exportModalLabel">Export Leads</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.export') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="leads">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_format" class="form-label">Export Format</label>
                        <select class="form-select" id="export_format" name="format">
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_degree" class="form-label">Degree of Interest Filter</label>
                        <select class="form-select" id="export_degree" name="degree_of_interest">
                            <option value="">All Degrees</option>
                            <option value="Cold">Cold</option>
                            <option value="Warm">Warm</option>
                            <option value="Hot">Hot</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_customer" class="form-label">Customer Status Filter</label>
                        <select class="form-select" id="export_customer" name="is_customer">
                            <option value="">All Statuses</option>
                            <option value="0">Not Customer</option>
                            <option value="1">Customer</option>
                        </select>
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
                <h5 class="modal-title" id="importModalLabel">Import Leads</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="leads">
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
