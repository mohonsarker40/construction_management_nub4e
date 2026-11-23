@extends('frontend.master')
@section('title', 'Check Request Status | Buildwise')
@section('styles')
    .request-wrap { width:min(760px,calc(100% - 36px)); margin:60px auto; }
    .request-card { padding:clamp(24px,5vw,44px); border:1px solid var(--line); border-radius:16px; background:#fff; }
    .request-card h1 { color:var(--green); }
    .request-card label { display:block; margin:14px 0 5px; font-weight:700; }
    .request-card input { width:100%; padding:12px; border:1px solid #cbd4ce; border-radius:7px; font:inherit; }
    .form-error { margin-top:8px; color:#a42424; }
@endsection
@section('content')
<section class="request-wrap"><div class="request-card">
    <p class="eyebrow">Track your request</p><h1>Check request status</h1>
    <p>Enter your request number and the email address used in the request.</p>
    @if(session('request_received'))
        <div class="request-card" style="margin:18px 0;background:#eef4e6">
            <strong>Your request was received.</strong><br>
            Request number: <strong>{{ session('request_received.request_no') }}</strong><br>
            {{ session('request_received.project') }} · Unit {{ session('request_received.unit') }}<br>
            Total price ৳{{ number_format(session('request_received.agreed_amount'), 2) }} · Initial payment ৳{{ number_format(session('request_received.initial_payment_amount'), 2) }}
        </div>
    @endif
    @if($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
    <form method="post" action="{{ route('booking.request.status') }}">@csrf
        <label for="request_no">Request number</label><input id="request_no" name="request_no" value="{{ old('request_no') }}" required>
        <label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" required>
        <button class="button button-primary" style="margin-top:18px" type="submit">Check status</button>
    </form>
    @isset($bookingRequest)
        @php($statusLabels = ['pending_manager' => 'Waiting for Project Manager review', 'pending_accountant' => 'Project Manager accepted; waiting for Accountant payment check', 'approved' => 'Payment approved and booking confirmed', 'rejected' => 'Request was not approved'])
        <hr><h2>{{ $statusLabels[$bookingRequest->status] ?? $bookingRequest->status }}</h2>
        <p>{{ $bookingRequest->project->project_name }} · Unit {{ $bookingRequest->unit_label }}</p>
        <p>Total price: ৳ {{ number_format($bookingRequest->agreed_amount, 2) }} · Initial payment: ৳ {{ number_format($bookingRequest->initial_payment_amount, 2) }}</p>
        @if($bookingRequest->rejection_note)<p>Reason: {{ $bookingRequest->rejection_note }}</p>@endif
    @endisset
</div></section>
@endsection
