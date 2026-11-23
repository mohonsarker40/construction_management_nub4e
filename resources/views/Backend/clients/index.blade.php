@extends('Backend.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Clients</h1>
            <p class="mb-0 text-muted">Manage client accounts and their information.</p>
        </div>
        <span class="text-muted small">Clients join through the website; Admin approves their registration below.</span>
    </div>

    @include('Backend.partials.flash')

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-warning">Pending client registrations</h6></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered"><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th><th>Action</th></tr></thead><tbody>
            @forelse($pendingRegistrations as $pending)<tr><td>{{ $pending->full_name }}</td><td>{{ $pending->email }}</td><td>{{ $pending->phone ?: '—' }}</td><td>{{ $pending->created_at?->format('d M Y') ?? '—' }}</td><td><form method="post" action="{{ route('admin.clients.registrations.approve', $pending) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-success">Approve and activate</button></form></td></tr>@empty<tr><td colspan="5" class="text-center text-muted">কোনো pending নিবন্ধন নেই।</td></tr>@endforelse
            </tbody></table>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Client list</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead><tr><th>Name</th><th>Contact person</th><th>Email</th><th>Phone</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->client_name }}</td>
                            <td>{{ $record->contact_person ?: '—' }}</td>
                            <td>{{ $record->email ?: '—' }}</td>
                            <td>{{ $record->phone ?: '—' }}</td>
                            <td><span class="badge badge-{{ $record->status ? 'success' : 'secondary' }}">{{ $record->status ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('admin.clients.edit', $record) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.clients.destroy', $record) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this client?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No clients found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @include('Backend.partials.pagination', ['records' => $records])
        </div>
    </div>
</div>
@endsection
