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

    <!-- Leads Reports Page -->
    <div id="leads-reports">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Leads Reports</h2>
            <div class="d-flex">
                <input type="date" class="form-control me-2" id="reportsDateFrom">
                <input type="date" class="form-control" id="reportsDateTo">
                <button class="btn btn-primary ms-2">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> This section is temporarily unavailable. Please check back later.
                </div>
            </div>
        </div>
    </div>

@endsection
