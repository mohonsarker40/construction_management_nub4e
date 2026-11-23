@extends('Backend.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Dashboard</h1>
            <p class="mb-0 text-muted">Project, flat, booking and payment summary</p></div>
        <div class="d-flex align-items-center">
            <a class="btn btn-sm btn-primary" href="{{ route('reports.finance') }}">Financial report</a>
        </div>
    </div>

    <div class="row">
        @foreach([
            ['total_projects', $summary['projects'], 'primary', 'fa-building'],
            ['completed_projects', $summary['completed_projects'], 'success', 'fa-check-circle'],
            ['listed_flats', $summary['flats'], 'info', 'fa-home'],
            ['active_clients', $summary['clients'], 'secondary', 'fa-users'],
            ['pending_requests', $summary['requests'], 'warning', 'fa-clock'],
            ['confirmed_bookings', $summary['bookings'], 'primary', 'fa-handshake'],
            ['approved_income', '৳ '.number_format($summary['income'], 2), 'success', 'fa-coins'],
            ['pending_payments', '৳ '.number_format($summary['pending_payment'], 2), 'warning', 'fa-hourglass-half'],
            ['project_expenses', '৳ '.number_format($summary['expenses'], 2), 'danger', 'fa-file-invoice-dollar'],
        ] as [$key, $value, $color, $icon])
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-{{ $color }} shadow h-100 py-2">
                    <div class="card-body"><div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase mb-1">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $value }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas {{ $icon }} fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Approved payments in the last 6 months</h6>
                </div>
                <div class="card-body">
                    <div style="height:300px">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Booking request status</h6>
                </div><div class="card-body">
                    <div style="height:300px">
                        <canvas id="requestChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('Backend/vendor/chart.js/Chart.bundle.min.js') }}"></script>
<script>
    const incomeChart = document.getElementById('incomeChart');
    new Chart(incomeChart, {
        type: 'line',
        data: {
            labels: @json($monthlyLabels),
            datasets: [
                {
                    label: @json('Approved income') + ' (৳)',
                    data: @json($monthlyIncome),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, .12)',
                    pointBackgroundColor: '#4e73df', fill: true,
                    lineTension: 0.25
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            legend: {
                display: false
            },
            scales: {
                yAxes: [{ticks: {beginAtZero: true}}]
            }
        }
    });

    new Chart(document.getElementById('requestChart'), {
        type: 'doughnut',
        data: {
            labels: [
                @json('Manager approval'),
                @json('Accountant approval'),
                @json('Approved'),
                @json('Rejected')
            ],
            datasets: [{
                data: @json(array_values($requestCounts)),
                backgroundColor: ['#f6c23e', '#36b9cc', '#1cc88a', '#e74a3b']
            }]
        },
        options: {
            maintainAspectRatio: false,
            legend: {
                position: 'bottom'
            }
        }
    });
</script>
@endsection
