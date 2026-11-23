<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Client;
use App\Models\ClientPayment;
use App\Models\Expense;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingWorkflowController extends Controller
{
    private function allow(array $roles): int
    {
        $roleId = (int) auth()->user()->role_id;
        abort_unless(in_array($roleId, $roles, true), 403);
        return $roleId;
    }

    private function managedProjectIds()
    {
        return Project::where('manager_user_id', auth()->id())->pluck('id');
    }

    public function projectIndex()
    {
        $this->allow([1]);
        $roleId = 1;
        $projects = Project::with(['manager', 'designer'])->withSum('expenses', 'amount')->orderBy('project_name')->get();
        $managers = User::where('role_id', 2)->where('status', 1)->orderBy('full_name')->get();
        $designers = User::where('role_id', 4)->where('status', 1)->orderBy('full_name')->get();
        $requests = collect();
        $bookings = collect();
        $pendingPayments = collect();
        $showProjectsOnly = true;

        return view('Backend.bookings.index', compact('roleId', 'projects', 'managers', 'designers', 'requests', 'bookings', 'pendingPayments', 'showProjectsOnly'));
    }

    public function index()
    {
        $roleId = $this->allow([1, 2, 4, 7]);
        $showProjectsOnly = false;
        $projectIds = $roleId === 2 ? $this->managedProjectIds() : ($roleId === 4 ? Project::where('designer_user_id', auth()->id())->pluck('id') : null);

        $projects = Project::with(['manager', 'designer'])->withSum('expenses', 'amount')
            ->when($projectIds !== null, fn ($query) => $query->whereIn('id', $projectIds))
            ->orderBy('project_name')->get();
        $clients = $roleId === 1 ? Client::where('status', 1)->orderBy('client_name')->get() : collect();
        $managers = $roleId === 1 ? User::where('role_id', 2)->where('status', 1)->orderBy('full_name')->get() : collect();
        $designers = $roleId === 1 ? User::where('role_id', 4)->where('status', 1)->orderBy('full_name')->get() : collect();

        $requests = BookingRequest::with(['project', 'client', 'booking'])
            ->when($projectIds !== null, fn ($query) => $query->whereIn('project_id', $projectIds))
            ->orderByDesc('created_at')->get();

        $bookingsQuery = Booking::with(['project', 'client', 'payments']);
        if ($projectIds !== null) $bookingsQuery->whereIn('project_id', $projectIds);
        $bookings = $bookingsQuery->orderByDesc('booking_date')->get();

        $pendingPaymentsQuery = ClientPayment::with('booking.project', 'booking.client')->where('status', ClientPayment::PENDING);
        if ($projectIds !== null) $pendingPaymentsQuery->whereHas('booking', fn ($q) => $q->whereIn('project_id', $projectIds));
        $pendingPayments = $pendingPaymentsQuery->orderBy('payment_date')->get();

        return view('Backend.bookings.index', compact('roleId', 'projects', 'clients', 'managers', 'designers', 'requests', 'bookings', 'pendingPayments', 'showProjectsOnly'));
    }

    public function storeProject(Request $request)
    {
        $this->allow([1]);
        $data = Project::validatedData($request);
        $unitTotalCents = $this->validateUnitLines($data['available_units']);
        if ($unitTotalCents > (int) round((float) $data['budget'] * 100)) {
            throw ValidationException::withMessages(['available_units' => 'সব flat-এর মোট দাম project budget-এর বেশি হতে পারবে না।']);
        }
        if (!empty($data['manager_user_id'])) {
            abort_unless(User::whereKey($data['manager_user_id'])->where('role_id', 2)->exists(), 422, 'নির্বাচিত অ্যাকাউন্টটি Project Manager হতে হবে।');
        }
        abort_unless(User::whereKey($data['designer_user_id'])->where('role_id', 4)->where('status', 1)->exists(), 422, 'নির্বাচিত অ্যাকাউন্টটি সক্রিয় Designer/Architect হতে হবে।');
        $data['created_by'] = auth()->id();
        Project::create($data);
        return back()->with('success', 'Project saved. Clients can now send a request from the website.');
    }

    private function validateUnitLines(string $lines): int
    {
        $labels = [];
        $totalCents = 0;
        foreach (preg_split('/\\r\\n|\\r|\\n/', $lines) as $line) {
            if (trim($line) === '') continue;
            $parts = array_map('trim', explode('|', $line, 2));
            if (count($parts) !== 2 || $parts[0] === '' || strlen($parts[0]) > 50 || !is_numeric($parts[1]) || (float) $parts[1] <= 0) {
                throw ValidationException::withMessages(['available_units' => 'প্রতিটি ফ্ল্যাটের নম্বর ও সঠিক দাম দিতে হবে।']);
            }
            if (in_array(strtolower($parts[0]), $labels, true)) {
                throw ValidationException::withMessages(['available_units' => 'একই ফ্ল্যাট নম্বর একাধিকবার দেওয়া যাবে না।']);
            }
            $labels[] = strtolower($parts[0]);
            $totalCents += (int) round((float) $parts[1] * 100);
        }
        if (empty($labels)) throw ValidationException::withMessages(['available_units' => 'কমপক্ষে একটি ফ্ল্যাট যোগ করুন।']);
        return $totalCents;
    }

    public function completeProject(Request $request, Project $project)
    {
        $roleId = $this->allow([2, 4]);
        $assignedUserId = $roleId === 2 ? $project->manager_user_id : $project->designer_user_id;
        abort_unless((int) $assignedUserId === (int) auth()->id(), 403);
        $data = $request->validate(['actual_end_date' => ['required', 'date', 'before_or_equal:today']]);
        $project->update(['actual_end_date' => $data['actual_end_date']]);
        return back()->with('success', 'প্রজেক্ট সম্পন্ন হিসেবে চিহ্নিত হয়েছে।');
    }

    public function assignManager(Request $request, Project $project)
    {
        $this->allow([1]);
        $data = $request->validate(['manager_user_id' => ['nullable', 'exists:users,id']]);
        if (!empty($data['manager_user_id'])) {
            abort_unless(User::whereKey($data['manager_user_id'])->where('role_id', 2)->exists(), 422);
        }
        $project->update(['manager_user_id' => $data['manager_user_id'] ?? null]);
        return back()->with('success', 'Project manager assignment saved.');
    }

    public function assignDesigner(Request $request, Project $project)
    {
        $this->allow([1]);
        $data = $request->validate(['designer_user_id' => ['required', 'exists:users,id']]);
        abort_unless(User::whereKey($data['designer_user_id'])->where('role_id', 4)->where('status', 1)->exists(), 422);
        $project->update(['designer_user_id' => $data['designer_user_id']]);
        return back()->with('success', 'Designer/Architect assignment saved.');
    }

    public function reviewByManager(Request $request, BookingRequest $bookingRequest)
    {
        $roleId = $this->allow([1, 2]);
        $data = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'rejection_note' => ['nullable', 'string', 'max:255'],
        ]);
        abort_unless($bookingRequest->status === BookingRequest::PENDING_MANAGER, 422, 'This request has already been reviewed by the Project Manager.');
        if ($roleId === 2) {
            abort_unless((int) $bookingRequest->project->manager_user_id === (int) auth()->id(), 403);
        }

        $bookingRequest->update([
            'status' => $data['decision'] === 'approve' ? BookingRequest::PENDING_ACCOUNTANT : BookingRequest::REJECTED,
            'manager_reviewed_by' => auth()->id(),
            'manager_reviewed_at' => now(),
            'rejection_note' => $data['decision'] === 'reject' ? ($data['rejection_note'] ?? 'Not approved by Project Manager.') : null,
        ]);
        return back()->with('success', $data['decision'] === 'approve' ? 'Request sent to Accountant for payment approval.' : 'Request rejected.');
    }

    public function reviewByAccountant(Request $request, BookingRequest $bookingRequest)
    {
        $this->allow([7]);
        $data = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'rejection_note' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($bookingRequest, $data) {
            $lockedRequest = BookingRequest::whereKey($bookingRequest->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedRequest->status === BookingRequest::PENDING_ACCOUNTANT, 422, 'This request is not waiting for Accountant review.');

            if ($data['decision'] === 'reject') {
                $lockedRequest->update([
                    'status' => BookingRequest::REJECTED,
                    'accountant_reviewed_by' => auth()->id(),
                    'accountant_reviewed_at' => now(),
                    'rejection_note' => $data['rejection_note'] ?? 'Payment could not be verified.',
                ]);
                return;
            }

            $booking = Booking::create([
                'booking_request_id' => $lockedRequest->id,
                'project_id' => $lockedRequest->project_id,
                'client_id' => $lockedRequest->client_id,
                'unit_label' => $lockedRequest->unit_label,
                'agreed_amount' => $lockedRequest->agreed_amount,
                'booking_date' => now()->toDateString(),
                'status' => Booking::BOOKED,
                'created_by' => auth()->id(),
            ]);

            ClientPayment::create([
                'booking_id' => $booking->id,
                'amount' => $lockedRequest->initial_payment_amount,
                'payment_date' => now()->toDateString(),
                'payment_method' => $lockedRequest->payment_method,
                'reference_no' => $lockedRequest->payment_reference ?: $lockedRequest->request_no,
                'status' => ClientPayment::APPROVED,
                'recorded_by' => auth()->id(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $lockedRequest->update([
                'status' => BookingRequest::APPROVED,
                'accountant_reviewed_by' => auth()->id(),
                'accountant_reviewed_at' => now(),
                'rejection_note' => null,
            ]);
        });

        return back()->with('success', $data['decision'] === 'approve' ? 'Payment verified and booking confirmed.' : 'Request rejected.');
    }

    public function storePayment(Request $request)
    {
        $this->allow([7]);
        $data = ClientPayment::validatedData($request);
        $booking = Booking::findOrFail($data['booking_id']);
        $receivedAndPending = ClientPayment::where('booking_id', $booking->id)->whereIn('status', [ClientPayment::PENDING, ClientPayment::APPROVED])->sum('amount');
        if ($receivedAndPending + $data['amount'] > $booking->agreed_amount) {
            return back()->withErrors(['amount' => 'Payment is greater than the remaining booking balance.'])->withInput();
        }
        $data['status'] = ClientPayment::PENDING;
        $data['recorded_by'] = auth()->id();
        ClientPayment::create($data);
        return back()->with('success', 'Payment recorded and sent for approval.');
    }

    public function approvePayment(ClientPayment $payment)
    {
        $this->allow([7]);
        abort_unless($payment->status === ClientPayment::PENDING, 422, 'Only a pending payment can be approved.');
        $payment->update(['status' => ClientPayment::APPROVED, 'approved_by' => auth()->id(), 'approved_at' => now()]);
        return back()->with('success', 'Payment approved and included in reports.');
    }

    public function storeExpense(Request $request)
    {
        $roleId = $this->allow([1, 2, 4, 7]);
        $data = Expense::validatedData($request);
        if ($roleId === 2) abort_unless(Project::whereKey($data['project_id'])->whereIn('id', $this->managedProjectIds())->exists(), 403);
        if ($roleId === 4) abort_unless(Project::whereKey($data['project_id'])->where('designer_user_id', auth()->id())->exists(), 403);
        $data['recorded_by'] = auth()->id();
        Expense::create($data);
        return back()->with('success', 'Project expense recorded.');
    }

    public function report()
    {
        $roleId = $this->allow([1, 2, 4, 7, 8]);
        if ($roleId === 8) {
            $user = auth()->user();
            $client = Client::where('user_id', $user->id)->where('status', 1)->firstOrFail();
            $bookings = Booking::with(['project', 'payments' => fn ($q) => $q->where('status', ClientPayment::APPROVED)])
                ->where('client_id', $client->id)->orderByDesc('booking_date')->get();
            return view('Backend.bookings.client', compact('bookings'));
        }

        $projectIds = $roleId === 2 ? $this->managedProjectIds() : ($roleId === 4 ? Project::where('designer_user_id', auth()->id())->pluck('id') : Project::pluck('id'));
        $projects = Project::with('manager')->withSum('expenses', 'amount')->whereIn('id', $projectIds)->orderBy('project_name')->get();
        foreach ($projects as $project) {
            $project->flat_value_total = collect($project->listedUnits())->sum('price');
            $project->revenue = ClientPayment::where('status', ClientPayment::APPROVED)
                ->whereHas('booking', fn ($q) => $q->where('project_id', $project->id))->sum('amount');
        }
        $bookings = Booking::with(['project', 'client', 'payments' => fn ($q) => $q->where('status', ClientPayment::APPROVED)])
            ->whereIn('project_id', $projectIds)->orderByDesc('booking_date')->get();
        $payments = ClientPayment::with('booking.project', 'booking.client')->where('status', ClientPayment::APPROVED)
            ->whereHas('booking', fn ($q) => $q->whereIn('project_id', $projectIds))->orderByDesc('payment_date')->get();
        $revenueTotal = $payments->sum('amount');
        $expenseTotal = Expense::whereIn('project_id', $projectIds)->sum('amount');
        return view('Backend.bookings.report', compact('roleId', 'projects', 'bookings', 'payments', 'revenueTotal', 'expenseTotal'));
    }
}
