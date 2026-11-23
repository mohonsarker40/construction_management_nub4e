<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Client;
use App\Models\ClientPayment;
use App\Models\Expense;
use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $roleId = (int) $user->role_id;
        $clientId = $roleId === 8 ? Client::where('user_id', $user->id)->value('id') : null;

        $projectsQuery = Project::query();
        if ($roleId === 2) $projectsQuery->where('manager_user_id', $user->id);
        if ($roleId === 4) $projectsQuery->where('designer_user_id', $user->id);
        if ($roleId === 8) {
            $projectIdsForClient = Booking::where('client_id', $clientId)->pluck('project_id')
                ->merge(BookingRequest::where('client_id', $clientId)->pluck('project_id'))->unique();
            $projectsQuery->whereIn('id', $projectIdsForClient);
        }
        $projects = $projectsQuery->get();
        $projectIds = $projects->pluck('id');

        $requestsQuery = BookingRequest::whereIn('project_id', $projectIds);
        $bookingsQuery = Booking::whereIn('project_id', $projectIds);
        $paymentsQuery = ClientPayment::whereHas('booking', fn ($query) => $query->whereIn('project_id', $projectIds));
        if ($roleId === 8) {
            $requestsQuery->where('client_id', $clientId);
            $bookingsQuery->where('client_id', $clientId);
            $paymentsQuery->whereHas('booking', fn ($query) => $query->where('client_id', $clientId));
        }

        $requestCounts = [
            BookingRequest::PENDING_MANAGER => (clone $requestsQuery)->where('status', BookingRequest::PENDING_MANAGER)->count(),
            BookingRequest::PENDING_ACCOUNTANT => (clone $requestsQuery)->where('status', BookingRequest::PENDING_ACCOUNTANT)->count(),
            BookingRequest::APPROVED => (clone $requestsQuery)->where('status', BookingRequest::APPROVED)->count(),
            BookingRequest::REJECTED => (clone $requestsQuery)->where('status', BookingRequest::REJECTED)->count(),
        ];

        $approvedPayments = (clone $paymentsQuery)->where('status', ClientPayment::APPROVED)->get(['amount', 'payment_date']);
        $from = now()->startOfMonth()->subMonths(5);
        $monthlyPayments = $approvedPayments->filter(fn ($payment) => $payment->payment_date >= $from->toDateString())
            ->groupBy(fn ($payment) => \Illuminate\Support\Carbon::parse($payment->payment_date)->format('Y-m'));
        $monthlyLabels = [];
        $monthlyIncome = [];
        for ($month = $from->copy(); $month <= now()->startOfMonth(); $month->addMonth()) {
            $key = $month->format('Y-m');
            $monthlyLabels[] = $month->format('M Y');
            $monthlyIncome[] = round($monthlyPayments->get($key, collect())->sum('amount'), 2);
        }

        $totalFlatCount = $projects->sum(fn ($project) => count($project->listedUnits()));
        $activeClientCount = $roleId === 8 ? ($clientId ? 1 : 0) : Client::where('status', 1)->count();
        $summary = [
            'projects' => $projects->count(),
            'completed_projects' => $projects->filter(fn ($project) => $project->actual_end_date !== null)->count(),
            'flats' => $totalFlatCount,
            'clients' => $activeClientCount,
            'requests' => array_sum([$requestCounts[BookingRequest::PENDING_MANAGER], $requestCounts[BookingRequest::PENDING_ACCOUNTANT]]),
            'bookings' => (clone $bookingsQuery)->count(),
            'income' => $approvedPayments->sum('amount'),
            'pending_payment' => (clone $paymentsQuery)->where('status', ClientPayment::PENDING)->sum('amount'),
            'expenses' => Expense::whereIn('project_id', $projectIds)->sum('amount'),
        ];

        return view('Backend.dashboard', compact('summary', 'requestCounts', 'monthlyLabels', 'monthlyIncome'));
    }

    public function index()
    {

    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
