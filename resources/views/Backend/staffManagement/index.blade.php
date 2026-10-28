@extends('Backend.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800">Staff</h1>
                <p class="mb-0 text-muted">
                    Manage staff accounts and their information.
                </p>
            </div>
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#addStaffModal" onclick="addStaff()">
                <i class="fas fa-plus fa-sm mr-1"></i>Add Staff
            </button>
        </div>

        @include('Backend.partials.flash')

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please check the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Staff List</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="thead-light">
                        <tr>
                            <th>SI</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Designation</th>
                            <th>Specialization</th>
                            <th>Daily Rate</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>{{ $records->firstItem() + $loop->index }}</td>
                                <td>{{ $record->full_name }}</td>
                                <td>{{ $record->email ?: '—' }}</td>
                                <td>{{ $record->phone ?: '—' }}</td>
                                <td>{{ $record->designation ?: '—' }}</td>
                                <td>{{ $record->specialization ?: '—' }}</td>
                                <td>{{ $record->daily_rate ?? '—' }}</td>
                                <td>
                                <span class="badge badge-{{ $record->status ? 'success' : 'secondary' }}">
                                    {{ $record->status ? 'Active' : 'Inactive' }}
                                </span>
                                </td>
                                <td class="text-right text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#addStaffModal" data-id="{{ $record->id }}" data-update-url="{{ route('admin.staffManagement.update', $record->id) }}"
                                            data-full-name="{{ $record->full_name }}"
                                            data-email="{{ $record->email }}"
                                            data-phone="{{ $record->phone }}"
                                            data-designation="{{ $record->designation }}"
                                            data-specialization="{{ $record->specialization }}"
                                            data-daily-rate="{{ $record->daily_rate }}"
                                            data-status="{{ $record->status }}"
                                            onclick="editStaff(this)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.staffManagement.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this staff member?')">@csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    No staff members found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @include('Backend.partials.pagination', ['records' => $records])
            </div>
        </div>
    </div>

    {{-- Add Staff Modal --}}
    <div class="modal fade" id="addStaffModal" tabindex="-1"
         role="dialog" aria-labelledby="addStaffModalLabel"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="staffForm"
                      action="{{ route('admin.staffManagement.store') }}"
                      method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="staffMethod" value="">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addStaffModalLabel"><i class="fas fa-user-plus mr-2"></i>
                            <span id="modalTitle">Add New Staff</span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid">

                            <div class="form-group row align-items-center">
                                <label for="full_name" class="col-md-4 col-form-label">Full Name <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="full_name" id="full_name" class="form-control" value="{{ old('full_name') }}" placeholder="Enter full name" required>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="phone" class="col-md-4 col-form-label">Phone Number<span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="Enter phone number">
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="email" class="col-md-4 col-form-label">Email Address<span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="Enter email address">
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="designation" class="col-md-4 col-form-label">Designation</label>
                                <div class="col-md-8">
                                    <input type="text" name="designation" id="designation" class="form-control" value="{{ old('designation') }}" placeholder="Enter Designation">
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="specialization" class="col-md-4 col-form-label">Specialization</label>
                                <div class="col-md-8">
                                    <input type="text" name="specialization" id="specialization" class="form-control" value="{{ old('specialization') }}" placeholder="Enter Specialization">
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="daily_rate" class="col-md-4 col-form-label">Daily Rate</label>
                                <div class="col-md-8">
                                    <input type="number" name="daily_rate" id="daily_rate" class="form-control" value="{{ old('daily_rate') }}" min="0" step="0.01" placeholder="Enter daily rate">
                                </div>
                            </div>

                            <div class="form-group row align-items-center mb-0">
                                <label for="status" class="col-md-4 col-form-label">Status</label>
                                <div class="col-md-8">
                                    <select name="status" id="status" class="form-control">
                                        <option value="1"{{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0"{{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i><span id="submitButtonText">Save Staff</span></button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Reopen modal if validation fails --}}
    @if ($errors->any())
        <script>
            $(document).ready(function () {
                $('#addStaffModal').modal('show');
            });
        </script>
    @endif


@endsection

<script>

    function editStaff(button) {
        const form = document.getElementById('staffForm');

        form.action = "{{ url('admin/staffManagement') }}/" + button.dataset.id;

        document.getElementById('staffMethod').name = '_method';
        document.getElementById('staffMethod').value = 'PUT';

        document.getElementById('modalTitle').textContent = 'Edit Staff';
        document.getElementById('submitButtonText').textContent = 'Update Staff';

        document.getElementById('full_name').value = button.dataset.fullName || '';
        document.getElementById('email').value = button.dataset.email || '';
        document.getElementById('phone').value = button.dataset.phone || '';
        document.getElementById('designation').value = button.dataset.designation || '';
        document.getElementById('specialization').value = button.dataset.specialization || '';
        document.getElementById('daily_rate').value = button.dataset.dailyRate || '';
        document.getElementById('status').value = button.dataset.status ?? '1';
    }
    function addStaff() {
        const form = document.getElementById('staffForm');

        form.reset();
        form.action = "{{ route('admin.staffManagement.store') }}";

        document.getElementById('staffMethod').name = '_method';
        document.getElementById('staffMethod').value = '';

        document.getElementById('modalTitle').textContent = 'Add New Staff';
        document.getElementById('submitButtonText').textContent = 'Save Staff';
        document.getElementById('status').value = '1';
    }
</script>
