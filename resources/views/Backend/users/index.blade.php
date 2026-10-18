@extends('Backend.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Users</h1>
            <p class="mb-0 text-muted">Manage accounts and assign project roles.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm shadow-sm"><i class="fas fa-plus fa-sm mr-1"></i> Add user</a>
    </div>

    @include('Backend.partials.flash')

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">User list</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->full_name }}</td>
                            <td>{{ $record->email }}</td>
                            <td>{{ $record->role->role_name ?? '—' }}</td>
                            <td>{{ $record->phone ?: '—' }}</td>
                            <td><span class="badge badge-{{ $record->status ? 'success' : 'secondary' }}">{{ $record->status ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('admin.users.edit', $record) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @if(auth()->id() !== $record->id)
                                    <form action="{{ route('admin.users.destroy', $record) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No users found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @include('Backend.partials.pagination', ['records' => $records])
        </div>
    </div>
</div>
@endsection
