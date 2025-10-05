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
        <h2>Roles</h2>
        @if(auth()->user()->hasPermission('roles', 'create'))
            <a role="button" href="{{route("roles.create")}}" class="btn-primary">Add</a>
        @endif
    </div>


    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Role Code</th>
                    <th>Role Name</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{$role->id}}</td>
                        <td>{{$role->role_code}}</td>
                        <td>{{$role->role_name}}</td>
                        <td>{{$role->created_at}}</td>
                        <td>{{$role->updated_at}}</td>
                        <td>
                            @if(auth()->user()->hasPermission('roles', 'edit'))
                                <a href="{{route("roles.edit",["id" => $role->id])}}" role="button" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                            @endif
                            @if(auth()->user()->hasPermission('roles', 'delete'))
                                    <button onclick="confirmDelete('{{ $role->id }}', '{{ $role->role_name }}')" class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                    <!-- Hidden delete form -->
                                    <form id="delete-form-{{ $role->id }}"
                                          action="{{ route('roles.delete') }}"
                                          method="POST" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="role_id" value="{{$role->id}}">
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
        function confirmDelete(roleId, roleName) {
            Swal.fire({
                title: 'Are you sure?',
                html: `You are about to delete the role <strong>${roleName}</strong>. This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the delete form
                    document.getElementById(`delete-form-${roleId}`).submit();
                }
            });
        }
    </script>
@endsection
