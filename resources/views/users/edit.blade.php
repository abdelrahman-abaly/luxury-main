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
    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Edit User</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{route('users.update')}}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$user->user_id}}"/>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">User Name <span class="text-danger">*</span></label>
                                <input name="name" type="text" value="{{$user->name}}" class="form-control" placeholder="Enter User Name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">User Email<span class="text-danger">*</span></label>
                                <input name="email" type="email" class="form-control" value="{{$user->email}}" placeholder="Enter User Email" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password<span class="text-danger">*</span></label>
                                <input name="password" type="password" class="form-control" placeholder="Enter Password" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Avatar</label>
                                <input name="avatar" type="file" class="form-control" accept=".jpg, .jpeg, .png"/>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select User Role<span class="text-danger">*</span></label>
                                <select name="role" class="form-select" style="width: 150px;">
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}">{{$role->role_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-between">
                        <a role="button" href="{{route('users.index')}}"
                           class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn-primary" style="padding: 8px;">Submit</button>
                    </div>
                </form>
            </div>

        </div>

    </div>
@endsection
