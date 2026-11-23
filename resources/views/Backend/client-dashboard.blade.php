@extends('Backend.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800">আমার ফ্ল্যাট ও পেমেন্ট</h1>
                <p class="mb-0 text-muted">স্বাগতম, {{ $client->client_name }}। আপনার অনুরোধ, বুকিং ও পেমেন্টের অবস্থা এখানে দেখুন।</p>
            </div>
            <a href="{{ route('booking.request.form') }}" class="btn btn-primary">ফ্ল্যাট কিনতে অনুরোধ করুন</a>
        </div>

        @include('Backend.partials.flash')

        @php
            $approvedPaidTotal = $bookings->sum(fn ($booking) => $booking->payments->where('status', \App\Models\Booking\ClientPayment::APPROVED)->sum('amount'));
        @endphp

        <div class="row mb-4">
            <div class="col-md-4 mb-3"><div class="card border-left-primary shadow h-100"><div class="card-body"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">মোট অনুরোধ</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $requests->count() }}</div></div></div></div>
            <div class="col-md-4 mb-3"><div class="card border-left-success shadow h-100"><div class="card-body"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">নিশ্চিত বুকিং</div><div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bookings->count() }}</div></div></div></div>
            <div class="col-md-4 mb-3"><div class="card border-left-info shadow h-100"><div class="card-body"><div class="text-xs font-weight-bold text-info text-uppercase mb-1">অনুমোদিত পেমেন্ট</div><div class="h5 mb-0 font-weight-bold text-gray-800">৳ {{ number_format($approvedPaidTotal, 2) }}</div></div></div></div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">আমার ফ্ল্যাট কেনার অনুরোধ</h6></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>অনুরোধ নম্বর</th><th>প্রজেক্ট / ফ্ল্যাট</th><th>মূল্য</th><th>জমা দেওয়া প্রাথমিক টাকা</th><th>অবস্থা</th><th>তারিখ</th></tr></thead>
                    <tbody>
                        @forelse($requests as $request)
                            @php
                                $requestStatus = [
                                    'pending_manager' => ['ম্যানেজার যাচাই করছেন', 'warning'],
                                    'pending_accountant' => ['হিসাব বিভাগ যাচাই করছে', 'info'],
                                    'approved' => ['অনুমোদিত · বুকিং নিশ্চিত', 'success'],
                                    'rejected' => ['অনুমোদিত হয়নি', 'danger'],
                                ][$request->status] ?? [$request->status, 'secondary'];
                            @endphp
                            <tr>
                                <td>{{ $request->request_no }}
                                    @if($request->rejection_note)<br><small class="text-danger">কারণ: {{ $request->rejection_note }}</small>@endif
                                </td>
                                <td>{{ $request->project->project_name }} / {{ $request->unit_label }}</td>
                                <td>৳ {{ number_format($request->agreed_amount, 2) }}</td>
                                <td>৳ {{ number_format($request->initial_payment_amount, 2) }}</td>
                                <td><span class="badge badge-{{ $requestStatus[1] }}">{{ $requestStatus[0] }}</span></td>
                                <td>{{ $request->created_at?->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">এখনো কোনো ফ্ল্যাট কেনার অনুরোধ করেননি।</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">আমার নিশ্চিত বুকিং ও পেমেন্ট</h6></div>
            <div class="card-body">
                @forelse($bookings as $booking)
                    @php
                        $approvedPaid = $booking->payments->where('status', \App\Models\Booking\ClientPayment::APPROVED)->sum('amount');
                        $pendingPaid = $booking->payments->where('status', \App\Models\Booking\ClientPayment::PENDING)->sum('amount');
                    @endphp
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex flex-wrap justify-content-between">
                            <div><h6 class="font-weight-bold mb-1">{{ $booking->project->project_name }} · ফ্ল্যাট {{ $booking->unit_label }}</h6><small class="text-muted">বুকিং তারিখ: {{ $booking->booking_date }}</small></div>
                            <span class="badge badge-success align-self-start">বুকিং নিশ্চিত</span>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-3"><small class="text-muted">ফ্ল্যাটের মূল্য</small><div>৳ {{ number_format($booking->agreed_amount, 2) }}</div></div>
                            <div class="col-sm-3"><small class="text-muted">অনুমোদিত পরিশোধ</small><div>৳ {{ number_format($approvedPaid, 2) }}</div></div>
                            <div class="col-sm-3"><small class="text-muted">যাচাইাধীন পেমেন্ট</small><div>৳ {{ number_format($pendingPaid, 2) }}</div></div>
                            <div class="col-sm-3"><small class="text-muted">বাকি</small><div>৳ {{ number_format(max(0, $booking->agreed_amount - $approvedPaid), 2) }}</div></div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm table-bordered mb-0">
                                <thead><tr><th>পেমেন্ট তারিখ</th><th>পরিমাণ</th><th>পদ্ধতি</th><th>রেফারেন্স</th><th>অবস্থা</th></tr></thead>
                                <tbody>
                                    @forelse($booking->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date }}</td>
                                            <td>৳ {{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ str_replace('_', ' ', ucfirst($payment->payment_method)) }}</td>
                                            <td>{{ $payment->reference_no ?: '—' }}</td>
                                            <td><span class="badge badge-{{ $payment->status === \App\Models\Booking\ClientPayment::APPROVED ? 'success' : 'warning' }}">{{ $payment->status === \App\Models\Booking\ClientPayment::APPROVED ? 'অনুমোদিত' : 'যাচাইাধীন' }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted">এখনো কোনো পেমেন্ট জমা হয়নি।</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info mb-0">অনুরোধ অনুমোদন হলে আপনার নিশ্চিত বুকিং এখানে দেখা যাবে।</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
