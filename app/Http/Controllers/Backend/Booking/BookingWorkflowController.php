<?php

namespace App\Http\Controllers\Backend\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking\Booking;
use App\Models\Booking\BookingRequest;
use App\Models\Booking\ClientPayment;
use App\Models\Client\Client;
use App\Models\Finance\Expense;
use App\Models\Project\Project;
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
            ->whereHas('project', fn ($query) => $query->where('budget', '>', 0)->whereHas('expenses'))
            ->when($projectIds !== null, fn ($query) => $query->whereIn('project_id', $projectIds))
            ->orderByDesc('created_at')->get();

        $bookingsQuery = Booking::with(['project', 'client', 'payments'])->where('status', Booking::BOOKED);
        if ($projectIds !== null) {
            $bookingsQuery->whereIn('project_id', $projectIds);
        }
        $bookings = $bookingsQuery->orderByDesc('booking_date')->get();

        $pendingPaymentsQuery = ClientPayment::with('booking.project', 'booking.client')->where('status', ClientPayment::PENDING);
        if ($projectIds !== null) {
            $pendingPaymentsQuery->whereHas('booking', fn ($q) => $q->whereIn('project_id', $projectIds));
        }
        $pendingPayments = $pendingPaymentsQuery->orderBy('payment_date')->get();

        return view('Backend.bookings.index', compact('roleId', 'projects', 'clients', 'managers', 'designers', 'requests', 'bookings', 'pendingPayments', 'showProjectsOnly'));
    }

    public function storeProject(Request $request)
    {
        $this->allow([1]);
        $data = Project::validatedData($request);
        $unitTotalCents = collect($data['flats'])->sum(fn ($flat) => (int) round((float) $flat['price'] * 100));
        if ($unitTotalCents > (int) round((float) $data['budget'] * 100)) {
            throw ValidationException::withMessages(['flats' => 'সব flat-এর মোট দাম project budget-এর বেশি হতে পারবে না।']);
        }
        $flatNames = collect($data['flats'])->pluck('flat_name')->map(fn ($name) => mb_strtolower(trim($name)));
        if ($flatNames->unique()->count() !== $flatNames->count()) {
            throw ValidationException::withMessages(['flats' => 'একই ফ্ল্যাট নম্বর একাধিকবার দেওয়া যাবে না।']);
        }
        if (! empty($data['manager_user_id'])) {
            abort_unless(User::whereKey($data['manager_user_id'])->where('role_id', 2)->exists(), 422, 'নির্বাচিত অ্যাকাউন্টটি Project Manager হতে হবে।');
        }
        abort_unless(User::whereKey($data['designer_user_id'])->where('role_id', 4)->where('status', 1)->exists(), 422, 'নির্বাচিত অ্যাকাউন্টটি সক্রিয় Designer/Architect হতে হবে।');
        $data['created_by'] = auth()->id();
        DB::transaction(function () use ($data) {
            $flats = $data['flats'];
            unset($data['flats']);
            $project = Project::create($data);
            $project->flats()->createMany($flats);
        });

        return back()->with('success', 'Project saved. Clients can now send a request from the website.');
    }

    public function completeProject(Request $request, Project $project)
    {
        $roleId = $this->allow([2, 4]);
        $assignedUserId = $roleId === 2 ? $project->manager_user_id : $project->designer_user_id;
        abort_unless((int) $assignedUserId === (int) auth()->id(), 403);
        abort_unless(! $project->actual_end_date, 422, 'Project completion date is already set.');
        $data = $request->validate(['actual_end_date' => ['required', 'date', 'before_or_equal:today']]);
        $project->update(['actual_end_date' => $data['actual_end_date']]);

        return back()->with('success', 'প্রজেক্ট সম্পন্ন হিসেবে চিহ্নিত হয়েছে।');
    }

    public function assignManager(Request $request, Project $project)
    {
        $this->allow([1]);
        $data = $request->validate(['manager_user_id' => ['nullable', 'exists:users,id']]);
        if (! empty($data['manager_user_id'])) {
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
        abort_unless((float) $bookingRequest->project->budget > 0 && $bookingRequest->project->expenses()->exists(), 422, 'Manager review requires a project budget and at least one recorded expense.');
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

            abort_unless((int) round((float) $lockedRequest->initial_payment_amount * 100) === (int) round((float) $lockedRequest->agreed_amount * 100), 422, 'Only full flat-price payment can be approved.');
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

    public function approvePayment(ClientPayment $payment)
    {
        $this->allow([7]);
        abort_unless($payment->status === ClientPayment::PENDING, 422, 'Only a pending payment can be approved.');
        $booking = $payment->booking;
        $approved = (float) ClientPayment::where('booking_id', $booking->id)->where('status', ClientPayment::APPROVED)->sum('amount');
        abort_unless($approved === 0.0 && (int) round((float) $payment->amount * 100) === (int) round((float) $booking->agreed_amount * 100), 422, 'Only one full flat-price payment can be approved.');
        $payment->update(['status' => ClientPayment::APPROVED, 'approved_by' => auth()->id(), 'approved_at' => now()]);

        return back()->with('success', 'Payment approved and included in reports.');
    }

    public function storeExpense(Request $request)
    {
        $roleId = $this->allow([1, 2, 4, 7]);
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'expenses' => ['required', 'array', 'min:1'],
            'expenses.*.category' => ['required', 'in:1,2,3,4'],
            'expenses.*.amount' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'expenses.*.expense_date' => ['required', 'date'],
            'expenses.*.description' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $roleId) {
            $project = Project::whereKey($data['project_id'])->lockForUpdate()->firstOrFail();

            if ($roleId === 2) {
                abort_unless((int) $project->manager_user_id === (int) auth()->id(), 403);
            }
            if ($roleId === 4) {
                abort_unless((int) $project->designer_user_id === (int) auth()->id(), 403);
            }

            $budgetCents = (int) round((float) $project->budget * 100);
            $spentCents = (int) round((float) Expense::where('project_id', $project->id)->sum('amount') * 100);
            $amountCents = collect($data['expenses'])->sum(fn ($expense) => (int) round((float) $expense['amount'] * 100));

            if ($spentCents + $amountCents > $budgetCents) {
                $remaining = max(0, $budgetCents - $spentCents) / 100;
                throw ValidationException::withMessages([
                    'expenses' => 'এই project-এর বাকি budget ৳' . number_format($remaining, 2) . '। সব expense row মিলিয়ে এর বেশি যোগ করা যাবে না।',
                ]);
            }

            foreach ($data['expenses'] as $expense) {
                Expense::create([
                    ...$expense,
                    'project_id' => $project->id,
                    'recorded_by' => auth()->id(),
                ]);
            }
        });

        return back()->with('success', 'Project expenses recorded.');
    }

    public function report()
    {
        $roleId = $this->allow([1, 2, 4, 7, 8]);
        if ($roleId === 8) {
            $user = auth()->user();
            $client = Client::where('user_id', $user->id)->where('status', 1)->firstOrFail();
            $bookings = Booking::with(['project', 'payments' => fn ($q) => $q->where('status', ClientPayment::APPROVED)])
                ->where('client_id', $client->id)->where('status', Booking::BOOKED)->orderByDesc('booking_date')->get();

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
            ->whereIn('project_id', $projectIds)->where('status', Booking::BOOKED)->orderByDesc('booking_date')->get();
        $payments = ClientPayment::with('booking.project', 'booking.client')->where('status', ClientPayment::APPROVED)
            ->whereHas('booking', fn ($q) => $q->whereIn('project_id', $projectIds))->orderByDesc('payment_date')->get();
        $revenueTotal = $payments->sum('amount');
        $expenseTotal = Expense::whereIn('project_id', $projectIds)->sum('amount');

        return view('Backend.bookings.report', compact('roleId', 'projects', 'bookings', 'payments', 'revenueTotal', 'expenseTotal'));
    }
}
