@extends('Backend.layouts.app')
@section('content')
<div class="container-fluid"><h1 class="h3 mb-1 text-gray-800">My Bookings &amp; Payments</h1><p class="mb-4 text-muted">Only payments approved by the Accountant are counted as paid.</p>
    @forelse($bookings as $booking)@php($paid = $booking->payments->sum('amount'))
        <div class="card shadow mb-4"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">{{ $booking->project->project_name }} — Unit {{ $booking->unit_label }}</h6></div><div class="card-body">
            <p>Booking date: {{ $booking->booking_date }} · Total price: ৳ {{ number_format($booking->agreed_amount, 2) }} · Paid: ৳ {{ number_format($paid, 2) }} · Balance: ৳ {{ number_format(max(0, $booking->agreed_amount - $paid), 2) }}</p>
            <div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th></tr></thead><tbody>@forelse($booking->payments as $payment)<tr><td>{{ $payment->payment_date }}</td><td>৳ {{ number_format($payment->amount, 2) }}</td><td>{{ str_replace('_', ' ', ucfirst($payment->payment_method)) }}</td><td>{{ $payment->reference_no ?: '—' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted">No approved payment yet.</td></tr>@endforelse</tbody></table></div>
        </div></div>
    @empty<div class="alert alert-info">No booking is linked to your account yet.</div>@endforelse
</div>
@endsection
