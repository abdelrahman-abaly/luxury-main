@extends("layouts.main")

@section('content')
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
<div class="content-header">
    <h2>Users</h2>
    <div class="d-flex">
        @if(auth()->user()->hasPermission('users', 'create'))
        <a role="button" href="{{route("users.create")}}" class="btn-primary me-2">Add</a>
        @endif
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
            <a href="{{ route('import-export.template', ['type' => 'users']) }}" class="btn btn-warning">
                <i class="fas fa-file-download me-1"></i> Template
            </a>
        </div>
    </div>
</div>


<div class="card-body">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{$user->id}}</td>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->role->role_name}}</td>
                    <td>{{$user->created_at}}</td>
                    <td>{{$user->updated_at}}</td>
                    <td>
                        @if(auth()->user()->hasPermission('users', 'edit'))
                        <a href="{{route("users.edit",["id" => $user->user_id])}}" role="button" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                        @endif
                        @if(auth()->user()->hasPermission('users', 'delete'))
                        <button onclick="confirmDelete('{{ $user->user_id }}', '{{ $user->name }}')" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                        <!-- Hidden delete form -->
                        <form id="delete-form-{{ $user->user_id }}"
                            action="{{ route('users.delete') }}"
                            method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="user_id" value="{{$user->user_id}}">
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete(userId, userName) {
        Swal.fire({
            title: 'Are you sure?',
            html: `You are about to delete the user <strong>${userName}</strong>. This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the delete form
                document.getElementById(`delete-form-${userId}`).submit();
            }
        });
    }
</script>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.export') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="users">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_format" class="form-label">Export Format</label>
                        <select class="form-select" id="export_format" name="format">
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_role" class="form-label">Role Filter</label>
                        <input type="text" class="form-control" id="export_role" name="role_id" placeholder="Role ID">
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
                <h5 class="modal-title" id="importModalLabel">Import Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="users">
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
