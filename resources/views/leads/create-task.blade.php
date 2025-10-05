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

    <div id="scheduled-tasks">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Create Task</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{route('leads.store-task')}}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{auth()->user()->user_id}}" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Task Date <span class="text-danger">*</span></label>
                                <input name="date" type="date" class="form-control" placeholder="Enter User Name" required>
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Select Lead<span class="text-danger">*</span></label>
                                <select name="lead" class="form-select">
                                    @foreach($leads as $lead)
                                        <option value="{{$lead->lead_id}}">{{$lead->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-between">
                        <a role="button" href="{{route('leads.tasks')}}"
                           class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn-primary" style="padding: 8px;">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

@endsection
