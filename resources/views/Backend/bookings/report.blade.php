@extends('Backend.layouts.app')
@section('content')
<div class="container-fluid finance-report">
    <div class="d-flex align-items-center justify-content-between mb-4 report-heading">
        <div>
            <h1 class="h3 mb-1 text-gray-800">আয়-ব্যয়ের সংক্ষিপ্ত রিপোর্ট</h1>
            <p class="mb-0 text-muted">অনুমোদিত পেমেন্ট ও নথিভুক্ত প্রজেক্ট খরচের হিসাব · {{ now()->format('d M Y') }}</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="window.print()">প্রিন্ট</button>
    </div>
    <div class="row mb-4 report-totals">
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">মোট অনুমোদিত আয়
                    <div class="h5 mt-2">৳ {{ number_format($revenueTotal, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">মোট প্রজেক্ট খরচ
                    <div class="h5 mt-2">৳ {{ number_format($expenseTotal, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">আয় থেকে খরচ বাদে
                    <div class="h5 mt-2">৳ {{ number_format($revenueTotal - $expenseTotal, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <strong>প্রজেক্ট অনুযায়ী বাজেট, ফ্ল্যাট মূল্য, আয় ও খরচ</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>প্রজেক্ট</th>
                        <th>মোট বাজেট</th>
                        <th>মোট খরচ</th>
                        <th>বাকি বাজেট</th>
                        <th>ফ্ল্যাটগুলোর মোট মূল্য</th>
                        <th>অনুমোদিত আয়</th>
                        <th>আয় - খরচ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td>{{ $project->project_name }}</td>
                            <td>৳ {{ number_format($project->budget, 2) }}</td>
                            <td>৳ {{ number_format($project->expenses_sum_amount ?? 0, 2) }}</td>
                            <td>৳ {{ number_format($project->budget - ($project->expenses_sum_amount ?? 0), 2) }}</td>
                            <td>৳ {{ number_format($project->flat_value_total, 2) }}</td>
                            <td>৳ {{ number_format($project->revenue, 2) }}</td>
                            <td>৳ {{ number_format($project->revenue - ($project->expenses_sum_amount ?? 0), 2) }}</td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center">কোনো প্রজেক্ট নেই</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <strong>ক্লায়েন্টের ফ্ল্যাট ও পেমেন্ট সারাংশ</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ক্লায়েন্ট</th>
                        <th>প্রজেক্ট / ফ্ল্যাট</th>
                        <th>ফ্ল্যাটের দাম</th>
                        <th>পরিশোধ</th>
                        <th>বাকি</th>
                    </tr>
                </thead>
                <tbody>@forelse($bookings as $booking)@php($paid = $booking->payments->sum('amount'))
                    <tr>
                        <td>{{ $booking->client->client_name }}</td>
                        <td>{{ $booking->project->project_name }} / {{ $booking->unit_label }}</td>
                        <td>৳ {{ number_format($booking->agreed_amount, 2) }}</td>
                        <td>৳ {{ number_format($paid, 2) }}</td>
                        <td>৳ {{ number_format(max(0, $booking->agreed_amount - $paid), 2) }}</td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="5" class="text-center">এখনো নিশ্চিত বুকিং নেই</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>@media print { #wrapper #content-wrapper, #content-wrapper, .sidebar, .topbar, .report-heading button, footer { display:block !important; } .sidebar, .topbar, .report-heading button, footer, .no-print { display:none !important; } body, #content-wrapper { background:#fff !important; } .container-fluid { width:100% !important; padding:0 !important; } .card { box-shadow:none !important; break-inside:avoid; } }</style>
@endsection
