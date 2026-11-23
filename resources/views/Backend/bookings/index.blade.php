@extends('Backend.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4"><div><h1 class="h3 mb-1 text-gray-800">{{ $showProjectsOnly ? 'Projects & Design Assignment' : (in_array($roleId, [2, 4], true) ? 'Assigned Projects & Completion' : 'Client Bookings & Payments') }}</h1><p class="mb-0 text-muted">{{ $showProjectsOnly ? 'Create projects, assign a Designer/Architect, and set the expected finish date.' : (in_array($roleId, [2, 4], true) ? 'Update project costs and mark assigned work complete.' : 'Review client requests, payments, and project expenses.') }}</p></div><a class="btn btn-outline-primary btn-sm" href="{{ route('reports.finance') }}">View Reports</a></div>
        @include('Backend.partials.flash')
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

        @if($showProjectsOnly)
            <div class="card shadow mb-4"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Create New Project</h6></div><div class="card-body"><p class="text-muted">Create a project, assign a Designer/Architect, and set the expected finish date. Once the project is completed, its available flats will appear on the website and clients will be able to submit purchase requests.</p>
                    <form method="post" action="{{ route('projects.store') }}">@csrf
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Project name</label>
                                <input name="project_name" maxlength="150" class="form-control" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Project Type</label>
                                <select name="project_type" class="form-control" required>
                                    <option value="">Select Project Type</option>
                                    <option value="Residential">Residential</option>
                                    <option value="Commercial">Commercial</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Location</label>
                                <input name="location" maxlength="255" class="form-control" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Budget (৳)</label>
                                <input id="project_budget" name="budget" type="number" min="0.01" step="0.01" class="form-control" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Start date</label>
                                <input name="start_date" type="date" class="form-control" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Estimated finish date</label>
                                <input name="estimated_end_date" type="date" class="form-control" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Project Manager User</label>
                                <select name="manager_user_id" class="form-control">
                                    <option value="">Project Manager Name</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}">
                                            {{ $manager->full_name }} ({{ $manager->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Designer / Architect</label>
                                <select name="designer_user_id" class="form-control" required>
                                    <option value="">Choose Designer User</option>
                                    @foreach($designers as $designer)
                                        <option value="{{ $designer->id }}">
                                            {{ $designer->full_name }} ({{ $designer->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label>Flat Number and Price</label>
                                <div id="flat-rows">
                                    <div class="form-row flat-row mb-2">
                                        <div class="col-md-5">
                                            <input type="text" name="flats[0][flat_name]" class="form-control flat-number" maxlength="50" placeholder="Flat number, e.g. A-101" required>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="number" name="flats[0][price]" class="form-control flat-price" min="0.01" step="0.01" placeholder="Price (৳)" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger remove-flat">Remove</button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-flat" class="btn btn-sm btn-outline-primary">
                                    + Add Another Flat
                                </button>
                                    <p class="mb-0 mt-2">
                                        Total Price of Flats:
                                        <strong id="flat-total">
                                            ৳ 0.00
                                        </strong>
                                        <span id="budget-message" class="text-muted">
                                            · The total price must be within the project budget.
                                        </span>
                                    </p>
                                <small class="text-muted">
                                    These flat numbers and prices will appear on the website card when the project is completed.
                                </small>
                            </div>
                        </div>
                        <button id="save-project" class="btn btn-primary">Save project</button>
                    </form>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Project List and Details
                    </h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Budget</th>
                                <th>Number of Flats and Total Value</th>
                                <th>Dates</th>
                                <th>Manager / Designer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $project)@php($units = $project->listedUnits())
                            <tr>
                                <td>
                                    <strong>{{ $project->project_name }}</strong><br>
                                    {{ $project->project_type }} · {{ $project->location }}<br>
                                    <small>{{ $project->actual_end_date ? 'Completed' : 'In Progress' }}</small>
                                </td>
                                <td>৳ {{ number_format($project->budget, 2) }}</td>
                                <td>{{ count($units) }}<br>
                                    ৳ {{ number_format(collect($units)->sum('price'), 2) }}
                                </td>
                                <td>Start: {{ $project->start_date?->format('d M Y') ?? '—' }}<br>Estimated Finish Date: {{ $project->estimated_end_date?->format('d M Y') ?? '—' }}@if($project->actual_end_date)<br>Completed: {{ $project->actual_end_date->format('d M Y') }}@endif</td><td>Manager: {{ $project->manager->full_name ?? 'Not Assigned' }}<br>Designer: {{ $project->designer->full_name ?? 'Not Assigned' }}</td></tr>@empty<tr><td colspan="5" class="text-center text-muted">No projects have been created yet.</td></tr>@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($roleId === 2 && $projects->isEmpty())
            <div class="alert alert-info">
                No projects are assigned to your account yet. Ask Admin to assign a project to your Project Manager User account.
            </div>@endif

        @if(in_array($roleId, [2, 4], true))
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Assigned Projects</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Expected Completion</th>
                                <th>Budget</th>
                                <th>Recorded Expenses to Date</th>
                                <th>Budget Remaining</th>
                                <th>Work Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $project)
                                <tr>
                                    <td>
                                        {{ $project->project_name }}<br>
                                        <small>{{ $project->location }}</small>
                                    </td>
                                    <td>{{ $project->estimated_end_date?->format('d M Y') ?? '—' }}</td>
                                    <td>৳ {{ number_format($project->budget, 2) }}</td>
                                    <td>৳ {{ number_format($project->expenses_sum_amount ?? 0, 2) }}</td>
                                    <td>৳ {{ number_format($project->budget - ($project->expenses_sum_amount ?? 0), 2) }}</td>
                                    <td>
                                        @if(!$project->actual_end_date)
                                            <form method="post" action="{{ route('projects.completion', $project) }}" class="form-inline">
                                                @csrf @method('PATCH')
                                                <input name="actual_end_date" type="date" max="{{ date('Y-m-d') }}" class="form-control form-control-sm mr-2" required>
                                                <button class="btn btn-sm btn-success">Mark finished</button>
                                            </form>
                                        @else
                                            <small class="text-success">Finished {{ $project->actual_end_date->format('d M Y') }} · visible for flat purchase</small>
                                        @endif
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No projects have been assigned to you.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if(!$showProjectsOnly && in_array($roleId, [1, 2, 7], true))
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Client purchase requests</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Request</th>
                                <th>Client</th>
                                <th>Project / Unit</th>
                                <th>Total price</th>
                                <th>Initial payment</th>
                                <th>Method / reference</th>
                                <th>Status</th>
                                <th>Review</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($requests as $bookingRequest)
                            <tr>
                                <td>
                                    {{ $bookingRequest->request_no }}<br>
                                    <small>{{ $bookingRequest->created_at->format('d M Y') }}</small>
                                </td>
                                <td>
                                    {{ $bookingRequest->client->client_name }}<br>
                                    <small>{{ $bookingRequest->client->email }} · {{ $bookingRequest->client->phone }}</small>
                                </td>
                                <td>{{ $bookingRequest->project->project_name }} / {{ $bookingRequest->unit_label }}</td>
                                <td>৳ {{ number_format($bookingRequest->agreed_amount, 2) }}</td>
                                <td>
                                    ৳ {{ number_format($bookingRequest->initial_payment_amount, 2) }}<br>
                                    <small>Full payment required</small></td><td>{{ str_replace('_', ' ', ucfirst($bookingRequest->payment_method)) }}<br>
                                    <small>{{ $bookingRequest->payment_reference ?: 'No reference supplied' }}</small>
                                </td>
                                <td>
                                    {{ str_replace('_', ' ', ucfirst($bookingRequest->status)) }}
                                    @if($bookingRequest->rejection_note)<br>
                                        <small>{{ $bookingRequest->rejection_note }}</small>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    @if($bookingRequest->status === \App\Models\Booking\BookingRequest::PENDING_MANAGER && in_array($roleId, [1, 2], true))
                                        <form method="post" action="{{ route('booking-requests.manager-review', $bookingRequest) }}" class="mb-1">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="decision" value="approve">
                                            <button class="btn btn-sm btn-success">PM approve</button>
                                        </form>
                                        <form method="post" action="{{ route('booking-requests.manager-review', $bookingRequest) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="decision" value="reject">
                                            <input name="rejection_note" class="form-control form-control-sm mb-1" placeholder="Reason (optional)">
                                            <button class="btn btn-sm btn-outline-danger">Reject</button>
                                        </form>
                                    @elseif($bookingRequest->status === \App\Models\Booking\BookingRequest::PENDING_ACCOUNTANT && $roleId === 7)
                                        <form method="post" action="{{ route('booking-requests.accountant-review', $bookingRequest) }}" class="mb-1">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="decision" value="approve">
                                            <button class="btn btn-sm btn-success">Verify &amp; confirm</button>
                                        </form>
                                        <form method="post" action="{{ route('booking-requests.accountant-review', $bookingRequest) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="decision" value="reject">
                                            <input name="rejection_note" class="form-control form-control-sm mb-1" placeholder="Reason (optional)">
                                            <button class="btn btn-sm btn-outline-danger">Reject</button>
                                        </form>
                                    @else — @endif
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No booking requests found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif


        @if($roleId === 7)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Payments waiting for approval</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Project / Unit</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($pendingPayments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date }}</td>
                                <td>{{ $payment->booking->client->client_name }}</td>
                                <td>{{ $payment->booking->project->project_name }} / {{ $payment->booking->unit_label }}</td>
                                <td>৳ {{ number_format($payment->amount, 2) }}</td>
                                <td>{{ str_replace('_', ' ', ucfirst($payment->payment_method)) }}</td>
                                <td>
                                    <form method="post" action="{{ route('booking-payments.approve', $payment) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success">Approve payment</button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No payment is waiting.</td>
                            </tr>@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if(!$showProjectsOnly && in_array($roleId, [1, 2, 4, 7], true))
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Record project expense</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Choose a project, add all its expense rows, then save them together. Materials is selected by default.</p>
                    <form method="post" action="{{ route('project-expenses.store') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Project</label>
                                <select id="expense-project" name="project_id" class="form-control" required>
                                    <option value="">Choose project</option>
                                    @foreach($projects as $project)
                                        @php($spent = $project->expenses_sum_amount ?? 0)
                                        @php($remaining = $project->budget - $spent)
                                        <option value="{{ $project->id }}" data-remaining="{{ max(0, $remaining) }}">
                                            {{ $project->project_name }} — Budget ৳{{ number_format($project->budget, 2) }}, spent ৳{{ number_format($spent, 2) }}, remaining ৳{{ number_format($remaining, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <small id="expense-budget-hint" class="form-text text-muted">Select a project to see its remaining budget.</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="expense-rows">
                                <thead><tr><th>Category</th><th>Amount (৳)</th><th>Date</th><th>Description</th><th></th></tr></thead>
                                <tbody>
                                    <tr class="expense-row">
                                        <td><select name="expenses[0][category]" class="form-control" required><option value="1" selected>Materials</option><option value="2">Labor</option><option value="3">Overhead</option><option value="4">Other</option></select></td>
                                        <td><input name="expenses[0][amount]" type="number" min="0.01" step="0.01" class="form-control expense-amount" required></td>
                                        <td><input name="expenses[0][expense_date]" type="date" value="{{ date('Y-m-d') }}" class="form-control" required></td>
                                        <td><input name="expenses[0][description]" maxlength="255" class="form-control"></td>
                                        <td><button type="button" class="btn btn-outline-danger remove-expense" disabled>Remove</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" id="add-expense-row" class="btn btn-outline-secondary mr-2">Add row</button>
                        <button class="btn btn-outline-primary">Save all expenses</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
    @if($showProjectsOnly)
        <script>
            const flatRows = document.getElementById('flat-rows');
            const projectForm = document.querySelector('form[action="{{ route('projects.store') }}"]');
            const budgetField = document.getElementById('project_budget');
            const flatTotalLabel = document.getElementById('flat-total');
            const budgetMessage = document.getElementById('budget-message');

            function updateFlatTotal() {
                const rows = [...flatRows.querySelectorAll('.flat-row')];
                const total = rows.reduce((sum, row) => sum + (Number(row.querySelector('.flat-price').value) || 0), 0);
                const budget = Number(budgetField.value) || 0;
                flatTotalLabel.textContent = '৳ ' + total.toLocaleString('en-BD', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                budgetMessage.textContent = total > budget ? '· The total price of the flats exceeds the project budget.' : '· The total price must be within the project budget.';
                budgetMessage.classList.toggle('text-danger', total > budget);
                budgetMessage.classList.toggle('text-muted', total <= budget);
                return {rows, total, budget};
            }

            document.getElementById('add-flat').addEventListener('click', () => {
                const index = flatRows.querySelectorAll('.flat-row').length;
                flatRows.insertAdjacentHTML('beforeend', `<div class="form-row flat-row mb-2"><div class="col-md-5"><input name="flats[${index}][flat_name]" type="text" class="form-control flat-number" maxlength="50" placeholder="Flat number, e.g. A-101" required></div><div class="col-md-5"><input name="flats[${index}][price]" type="number" class="form-control flat-price" min="0.01" step="0.01" placeholder="Price (৳)" required></div><div class="col-md-2"><button type="button" class="btn btn-outline-danger remove-flat">Remove</button></div></div>`);
            });

            flatRows.addEventListener('input', updateFlatTotal);
            budgetField.addEventListener('input', updateFlatTotal);
            flatRows.addEventListener('click', event => {
                if (!event.target.classList.contains('remove-flat')) return;
                const row = event.target.closest('.flat-row');
                if (flatRows.querySelectorAll('.flat-row').length > 1) row.remove();
                else row.querySelectorAll('input').forEach(input => input.value = '');
                updateFlatTotal();
            });

            projectForm.addEventListener('submit', event => {
                const {rows, total, budget} = updateFlatTotal();
                const labels = rows.map(row => row.querySelector('.flat-number').value.trim());
                const duplicate = labels.some((label, index) => labels.findIndex(other => other.toLowerCase() === label.toLowerCase()) !== index);
                if (total > budget || duplicate) {
                    event.preventDefault();
                    budgetMessage.textContent = duplicate ? '· Duplicate flat numbers are not allowed.' : '· The total price of the flats cannot exceed the project budget.';
                    budgetMessage.classList.add('text-danger');
                    return;
                }
                rows.forEach((row, index) => { row.querySelector('.flat-number').name = `flats[${index}][flat_name]`; row.querySelector('.flat-price').name = `flats[${index}][price]`; });
            });
        </script>
    @endif
    @if(!$showProjectsOnly && in_array($roleId, [1, 2, 4, 7], true))
        <script>
            const expenseProject = document.getElementById('expense-project');
            const expenseBudgetHint = document.getElementById('expense-budget-hint');
            const expenseRows = document.querySelector('#expense-rows tbody');
            const expenseRowTemplate = expenseRows.querySelector('.expense-row').cloneNode(true);

            function updateExpenseBudget() {
                const selected = expenseProject.selectedOptions[0];
                const remaining = Number(selected.dataset.remaining);
                if (!selected.value) {
                    expenseRows.querySelectorAll('.expense-amount').forEach(input => input.removeAttribute('max'));
                    expenseBudgetHint.textContent = 'Select a project to see its remaining budget.';
                    return;
                }

                expenseRows.querySelectorAll('.expense-amount').forEach(input => input.max = remaining.toFixed(2));
                expenseBudgetHint.textContent = 'Remaining project budget: ৳' + remaining.toLocaleString('en-BD', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }

            function updateExpenseRows() {
                const rows = [...expenseRows.querySelectorAll('.expense-row')];
                rows.forEach((row, index) => {
                    row.querySelectorAll('[name]').forEach(input => input.name = input.name.replace(/expenses\[\d+\]/, `expenses[${index}]`));
                    row.querySelector('.remove-expense').disabled = rows.length === 1;
                });
                updateExpenseBudget();
            }

            document.getElementById('add-expense-row').addEventListener('click', () => {
                const row = expenseRowTemplate.cloneNode(true);
                row.querySelectorAll('input').forEach(input => {
                    if (input.type === 'date') input.value = '{{ date('Y-m-d') }}';
                    else input.value = '';
                });
                row.querySelector('select').value = '1';
                expenseRows.appendChild(row);
                updateExpenseRows();
            });

            expenseRows.addEventListener('click', event => {
                if (!event.target.classList.contains('remove-expense')) return;
                event.target.closest('.expense-row').remove();
                updateExpenseRows();
            });

            expenseProject.addEventListener('change', updateExpenseBudget);
            updateExpenseRows();
        </script>
    @endif
@endsection
