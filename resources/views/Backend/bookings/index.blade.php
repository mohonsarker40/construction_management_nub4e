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
                                            <input type="text" class="form-control flat-number" maxlength="50" placeholder="Flat number, e.g. A-101" required>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="number" class="form-control flat-price" min="0.01" step="0.01" placeholder="Price (৳)" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger remove-flat">Remove</button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-flat" class="btn btn-sm btn-outline-primary">
                                    + Add Another Flat
                                </button>
                                <input type="hidden" id="available_units" name="available_units">
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
                                <th>Recorded Expenses to Date</th>
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
                                    <td>৳ {{ number_format($project->expenses_sum_amount ?? 0, 2) }}</td>
                                    <td>
                                        <form method="post" action="{{ route('projects.completion', $project) }}" class="form-inline">
                                            @csrf @method('PATCH')
                                            <input name="actual_end_date" type="date" value="{{ $project->actual_end_date?->format('Y-m-d') }}" max="{{ date('Y-m-d') }}" class="form-control form-control-sm mr-2" required>
                                            <button class="btn btn-sm btn-success">Mark finished</button>
                                        </form>

                                        @if($project->actual_end_date)
                                            <small class="text-success">Finished {{ $project->actual_end_date->format('d M Y') }} · visible for flat purchase</small>
                                        @endif
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No projects have been assigned to you.</td>
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
                                    <small>{{ $bookingRequest->initial_payment_amount >= $bookingRequest->agreed_amount ? 'Full payment' : 'Part payment' }}</small></td><td>{{ str_replace('_', ' ', ucfirst($bookingRequest->payment_method)) }}<br>
                                    <small>{{ $bookingRequest->payment_reference ?: 'No reference supplied' }}</small>
                                </td>
                                <td>
                                    {{ str_replace('_', ' ', ucfirst($bookingRequest->status)) }}
                                    @if($bookingRequest->rejection_note)<br>
                                        <small>{{ $bookingRequest->rejection_note }}</small>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    @if($bookingRequest->status === \App\Models\BookingRequest::PENDING_MANAGER && in_array($roleId, [1, 2], true))
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
                                    @elseif($bookingRequest->status === \App\Models\BookingRequest::PENDING_ACCOUNTANT && $roleId === 7)
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

        @if(!$showProjectsOnly && $roleId === 7)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Record another client payment</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('booking-payments.store') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Booking</label>
                                <select name="booking_id" class="form-control" required><option value="">Choose booking</option>@foreach($bookings as $booking)@php($reservedPaid = $booking->payments->whereIn('status', [\App\Models\ClientPayment::APPROVED, \App\Models\ClientPayment::PENDING])->sum('amount'))<option value="{{ $booking->id }}">{{ $booking->client->client_name }} — {{ $booking->project->project_name }} / {{ $booking->unit_label }} (Balance ৳{{ number_format($booking->agreed_amount - $reservedPaid, 2) }})</option>@endforeach</select></div>
                            <div class="form-group col-md-2">
                                <label>Amount (৳)</label>
                                <input name="amount" type="number" min="0.01" step="0.01" class="form-control" required></div>
                            <div class="form-group col-md-2">
                                <label>Date</label>
                                <input name="payment_date" type="date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
                            <div class="form-group col-md-2">
                                <label>Method</label>
                                <select name="payment_method" class="form-control">
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                    <option value="mobile_banking">Mobile banking</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Reference</label>
                                <input name="reference_no" maxlength="100" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary">Save pending payment</button>
                    </form>
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
                    <p class="text-muted">Enter each expense separately. The total expenses will be added to the Designer’s project list and Reports.</p>
                    <form method="post" action="{{ route('project-expenses.store') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Project</label>
                                <select name="project_id" class="form-control" required>
                                    <option value="">Choose project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">
                                            {{ $project->project_name }}
                                        </option>@endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Category</label>
                                <select name="category" class="form-control">
                                    <option value="1">Materials</option>
                                    <option value="2">Labor</option>
                                    <option value="3">Overhead</option>
                                    <option value="4">Other</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Amount (৳)</label>
                                <input name="amount" type="number" min="0.01" step="0.01" class="form-control" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Date</label>
                                <input name="expense_date" type="date" value="{{ date('Y-m-d') }}" class="form-control" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Description</label>
                                <input name="description" maxlength="255" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-outline-primary">Save expense</button>
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
                flatRows.insertAdjacentHTML('beforeend', '<div class="form-row flat-row mb-2"><div class="col-md-5"><input type="text" class="form-control flat-number" maxlength="50" placeholder="Flat number, e.g. A-101" required></div><div class="col-md-5"><input type="number" class="form-control flat-price" min="0.01" step="0.01" placeholder="Price (৳)" required></div><div class="col-md-2"><button type="button" class="btn btn-outline-danger remove-flat">Remove</button></div></div>');
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
                document.getElementById('available_units').value = rows.map(row => row.querySelector('.flat-number').value.trim() + ' | ' + row.querySelector('.flat-price').value).join('\n');
            });
        </script>
    @endif
@endsection
