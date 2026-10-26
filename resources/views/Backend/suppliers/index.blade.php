@extends('Backend.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800">Suppliers</h1>
                <p class="mb-0 text-muted">
                    Manage supplier information, contact details, and supplier status.
                </p>
            </div>
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#addSupplierModal" onclick="addStaff()">
                <i class="fas fa-plus fa-sm mr-1"></i>Add Supplier
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
                <h6 class="m-0 font-weight-bold text-primary">Supplier List</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="thead-light">
                        <tr>
                            <th>SI</th>
                            <th>Supplier Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Address</th>
                            <th>GST Number</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>{{ $records->firstItem() + $loop->index }}</td>
                                <td>{{ $record->supplier_name }}</td>
                                <td>{{ $record->contact_person }}</td>
                                <td>{{ $record->email ?: '—' }}</td>
                                <td>{{ $record->phone ?: '—' }}</td>
                                <td>{{ $record->city ?: '—' }}</td>
                                <td>{{ $record->address ?: '—' }}</td>
                                <td>{{ $record->gst_number ?? '—' }}</td>
                                <td>
                                <span class="badge badge-{{ $record->status ? 'success' : 'secondary' }}">
                                    {{ $record->status ? 'Active' : 'Inactive' }}
                                </span>
                                </td>
                                <td class="text-right text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#addSupplierModal" data-id="{{ $record->id }}" data-update-url="{{ route('admin.staffManagement.update', $record->id) }}"
                                            data-supplier-name="{{ $record->supplier_name }}"
                                            data-contact-person="{{ $record->contact_person }}"
                                            data-email="{{ $record->email }}"
                                            data-phone="{{ $record->phone }}"
                                            data-city="{{ $record->city }}"
                                            data-address="{{ $record->address }}"
                                            data-gst-number="{{ $record->gst_number }}"
                                            data-status="{{ $record->status }}"
                                            onclick="editStaff(this)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.suppliers.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this Supplier?')">@csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
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
    <div class="modal fade" id="addSupplierModal" tabindex="-1"
         role="dialog" aria-labelledby="addSupplierModalLabel"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="supplierForm"
                      action="{{ route('admin.suppliers.store') }}"
                      method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="supplierMethod" value="">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addSupplierModalLabel"><i class="fas fa-user-plus mr-2"></i>
                            <span id="modalTitle">Add New Supplier</span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid">

                            <div class="form-group row align-items-center">
                                <label for="full_name" class="col-md-4 col-form-label">Supplier Name <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="supplier_name" id="supplier_name" class="form-control" value="{{ old('supplier_name') }}" placeholder="Enter Supplier Name" required>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="full_name" class="col-md-4 col-form-label">Contact Person <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ old('contact_person') }}" placeholder="Enter Contact Person" required>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="phone" class="col-md-4 col-form-label">Phone Number<span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="Enter phone number">
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="email" class="col-md-4 col-form-label">
                                    Email Address
                                </label>
                                <div class="col-md-8">
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="Enter email address">
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="designation" class="col-md-4 col-form-label">City</label>
                                <div class="col-md-8">
                                    <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}" placeholder="Enter City">
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="specialization" class="col-md-4 col-form-label">Address</label>
                                <div class="col-md-8">
                                    <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}" placeholder="Enter Address">
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="daily_rate" class="col-md-4 col-form-label">GST Number</label>
                                <div class="col-md-8">
                                    <input type="number" name="gst_number" id="gst_number" class="form-control" value="{{ old('gst_number') }}" min="0" step="0.01" placeholder="Enter GST Number">
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
                $('#addSupplierModal').modal('show');
            });
        </script>
    @endif


@endsection

<script>

    function editStaff(button) {
        const form = document.getElementById('supplierForm');

        form.action = "{{ url('admin/suppliers') }}/" + button.dataset.id;

        document.getElementById('supplierMethod').name = '_method';
        document.getElementById('supplierMethod').value = 'PUT';

        document.getElementById('modalTitle').textContent = 'Edit Supplier';
        document.getElementById('submitButtonText').textContent = 'Update Supplier';

        document.getElementById('supplier_name').value = button.dataset.supplierName || '';
        document.getElementById('contact_person').value = button.dataset.contactPerson || '';
        document.getElementById('email').value = button.dataset.email || '';
        document.getElementById('phone').value = button.dataset.phone || '';
        document.getElementById('city').value = button.dataset.city || '';
        document.getElementById('address').value = button.dataset.address || '';
        document.getElementById('gst_number').value = button.dataset.gstNumber || '';
        document.getElementById('status').value = button.dataset.status ?? '1';
    }
    function addStaff() {
        const form = document.getElementById('supplierForm');

        form.reset();
        form.action = "{{ route('admin.suppliers.store') }}";

        document.getElementById('supplierMethod').name = '_method';
        document.getElementById('supplierMethod').value = '';

        document.getElementById('modalTitle').textContent = 'Add New Supplier';
        document.getElementById('submitButtonText').textContent = 'Save Supplier';
        document.getElementById('status').value = '1';
    }
</script>
