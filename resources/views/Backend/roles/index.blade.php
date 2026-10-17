@extends('Backend.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Roles</h1>
            <p class="mb-0 text-muted">Manage access roles for project users.</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm shadow-sm"><i class="fas fa-plus fa-sm mr-1"></i> Add role</a>
    </div>

    @include('Backend.partials.flash')

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Role list</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead><tr><th>Role</th><th>Description</th><th>Users</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->role_name }}</td>
                            <td>{{ $record->description ?? '—' }}</td>
                            <td>{{ $record->users_count }}</td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('admin.roles.edit', $record) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.roles.destroy', $record) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this role?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" {{ $record->users_count ? 'disabled' : '' }}>Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No roles found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @include('Backend.partials.pagination', ['records' => $records])
        </div>
    </div>
</div>
@endsection
