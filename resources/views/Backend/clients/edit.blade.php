@extends('Backend.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit client</h1>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-light btn-sm">Back to clients</a>
    </div>
    @include('Backend.partials.flash')
    <div class="card shadow mb-4"><div class="card-body">
        <form action="{{ route('admin.clients.update', $client) }}" method="post">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="client_name">Client name</label>
                    <input id="client_name" name="client_name" type="text" maxlength="120" required value="{{ old('client_name', $client->client_name) }}" class="form-control @error('client_name') is-invalid @enderror">
                    @error('client_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="contact_person">Contact person</label>
                    <input id="contact_person" name="contact_person" type="text" maxlength="100" value="{{ old('contact_person', $client->contact_person) }}" class="form-control @error('contact_person') is-invalid @enderror">
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" maxlength="100" value="{{ old('email', $client->email) }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" type="text" maxlength="20" required value="{{ old('phone', $client->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input id="address" name="address" type="text" maxlength="255" value="{{ old('address', $client->address) }}" class="form-control @error('address') is-invalid @enderror">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required class="form-control @error('status') is-invalid @enderror">
                    <option value="1" @selected((string) old('status', $client->status) === '1')>Active</option>
                    <option value="0" @selected((string) old('status', $client->status) === '0')>Inactive</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button class="btn btn-primary" type="submit">Update client</button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div></div>
</div>
@endsection
