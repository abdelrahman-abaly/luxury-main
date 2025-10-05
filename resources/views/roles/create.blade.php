@extends("layouts.main")
@section('content')
    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Add Role</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{route('roles.store')}}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input name="role_name" type="text" class="form-control" placeholder="Enter Role Name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Role Code<span class="text-danger">*</span></label>
                                <input name="role_code" type="text" class="form-control" placeholder="Enter Role Code" required>
                            </div>
                        </div>
                    </div>
                    <h4 class="mt-4">Permissions</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Table</th>
                            <th>Read</th>
                            <th>Add</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tables as $table)
                            <tr>
                                <td>{{ Str::title(str_replace('_', ' ', $table)) }}</td>
                                @foreach($abilities as $ability)
                                    <td>
                                        @php
                                            $permission = $permissions[$table]->firstWhere('ability', $ability) ?? null;
                                        @endphp
                                        <input type="checkbox" name="permissions[]" value="{{ $permission?->id }}" />
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-between">
                        <a role="button" href="{{route('roles.index')}}"
                           class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn-primary" style="padding: 8px;">Submit</button>
                    </div>
                </form>
            </div>

        </div>

    </div>
@endsection
