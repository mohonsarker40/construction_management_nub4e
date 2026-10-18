@extends('Backend.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Client</h1>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-light btn-sm">Back to clients</a>
    </div>
    @include('Backend.partials.flash')
    <div class="card shadow mb-4"><div class="card-body">
        <form action="{{ route('admin.clients.store') }}" method="post">
            @csrf
            @include('Backend.clients.form')
            <button class="btn btn-primary" type="submit">Save client</button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div></div>
</div>
@endsection
