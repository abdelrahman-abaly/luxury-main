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

    <!-- Add New Lead Page -->
    <div id="add-lead">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Add New Lead</h2>
            <div class="d-flex">
                <form action="{{route('leads.index')}}" method="GET">
                    @csrf
                    <button class="btn btn-outline-secondary me-2">Cancel</button>
                </form>
                <button id="form-submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="leads-store-form" action="{{route('leads.store')}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lead Name</label>
                                <input name="name" type="text" class="form-control" placeholder="Enter lead name">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Numbers <span class="text-danger">*</span></label>
                                <input name="phone" type="tel" class="form-control" placeholder="Enter phone number" required>
                                <small class="text-muted">You can add multiple numbers separated by commas</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input name="email" type="email" class="form-control" placeholder="Enter email address">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Governorate</label>
                                <select name="governorate" class="form-select">
                                    <option selected disabled>Select governorate</option>
                                    <option value="Cairo">Cairo</option>
                                    <option value="Alexandria">Alexandria</option>
                                    <option value="Giza">Giza</option>
                                    <option value="Aswan">Aswan</option>
                                    <option value="Red Sea">Red Sea</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Source</label>
                                <select name="source" class="form-select">
                                    <option selected disabled>Select source</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="Phone">Phone</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="Instagram">Instagram</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Degree of Interest</label>
                                <select name="interest" class="form-select">
                                    <option selected disabled>Select interest level</option>
                                    <option value="Hot">Hot</option>
                                    <option value="Warm">Warm</option>
                                    <option value="Cold">Cold</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Next Follow Up</label>
                                <select name="next_follow_up" class="form-select">
                                    <option selected disabled>Select follow up period</option>
                                    <option value="Next 7 days">Next 7 days</option>
                                    <option value="Next 14 days">Next 14 days</option>
                                    <option value="Next 21 days">Next 21 days</option>
                                    <option value="Next 28 days">Next 28 days</option>
                                    <option value="Next 30 days">Next 30 days</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Interested Categories <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input name="categories[]" class="form-check-input" type="checkbox" id="category-watches" value="Watches">
                                <label class="form-check-label" for="category-watches">Watches</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input name="categories[]" class="form-check-input" type="checkbox" id="category-caps" value="Caps">
                                <label class="form-check-label" for="category-caps">Caps</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input name="categories[]" class="form-check-input" type="checkbox" id="category-bags" value="Bags">
                                <label class="form-check-label" for="category-bags">Bags</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input name="categories[]" class="form-check-input" type="checkbox" id="category-wallets" value="Wallets">
                                <label class="form-check-label" for="category-wallets">Wallets</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Interested Products (SKUs) <span class="text-danger">*</span></label>
                        <input name="skus" type="text" class="form-control" placeholder="Enter SKUs separated by commas" required>
                        <small class="text-muted">Example: SKU-123, SKU-456, SKU-789</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Any additional notes about this lead"></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section("scripts")
    <script>
        $("#form-submit").click(function() {
            $("#leads-store-form").submit();
        });
    </script>
@endsection
