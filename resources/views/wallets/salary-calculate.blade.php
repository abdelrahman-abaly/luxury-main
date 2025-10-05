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

    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Employee Salary Calculator</h4>
        </div>
        <div class="card-body">
            <form action="{{route('my-wallet.post-salary-calc')}}" method="POST" id="salaryCalculatorForm">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth()->user()->user_id }}">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="days_worked" class="form-label">Days Worked</label>
                        <input type="number" class="form-control" id="days_worked" name="days_worked" min="0" max="31" required>
                    </div>

                    <div class="col-md-4">
                        <label for="day_cost" class="form-label">Daily Rate (EGP)</label>
                        <input type="number" class="form-control" id="day_cost" name="day_cost" min="0" step="0.01" required>
                    </div>

                    <div class="col-md-4">
                        <label for="month" class="form-label">Month</label>
                        <select class="form-select" id="month" name="month" required>
                            <option value="" selected disabled>Select Month</option>
                            @foreach($months as $month)
                                @php
                                    $date = DateTime::createFromFormat('F', $month);
                                    $monthNumber = $date->format('n');
                                @endphp
                                <option value="{{ $monthNumber }}" {{ now()->format('F') == $month ? 'selected' : '' }}>{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-select" id="year" name="year" required>
                            <option value="" selected disabled>Select Year</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="salary_wallet" class="form-label">Calculated Salary (EGP)  (Without Borrow Calculate)</label>
                        <input type="text" class="form-control" id="salary_wallet" name="salary_wallet" readonly>
                    </div>
                </div>

                <div class="d-flex">
                    <button type="submit" class="btn btn-primary">Calculate Salary</button>
                    <button type="button" class="btn btn-secondary ms-2" onclick="cancelForm()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Calculate salary on input change
            document.getElementById('days_worked').addEventListener('input', calculateSalary);
            document.getElementById('day_cost').addEventListener('input', calculateSalary);

        });

        function calculateSalary() {
            const daysWorked = parseFloat(document.getElementById('days_worked').value) || 0;
            const dayCost = parseFloat(document.getElementById('day_cost').value) || 0;
            const salary = daysWorked * dayCost;

            document.getElementById('salary_wallet').value = salary.toFixed(2);
        }

        function cancelForm() {
            window.location.href = "/my-wallet/show"
        }

        // Optional: AJAX submission example
        function submitViaAjax() {
            const formData = new FormData(document.getElementById('salaryCalculatorForm'));

            fetch('/salary/calculate', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Salary saved successfully!');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
@endsection
