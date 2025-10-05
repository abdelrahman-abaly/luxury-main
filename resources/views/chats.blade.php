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

    <!-- Chats Page -->
    <div id="chats">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Chats</h2>
            <div class="d-flex">
                <div class="input-group me-2" style="width: 300px;">
                    <input type="text" class="form-control" placeholder="Search chats...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <button class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> New Chat
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> This section will display your chat conversations with customers and leads.
                </div>
            </div>
        </div>
    </div>

@endsection
