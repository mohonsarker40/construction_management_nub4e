<?php

namespace App\Http\Controllers;

use App\Models\Booking\Booking;
use App\Models\Booking\BookingRequest;
use App\Models\Client\Client;
use App\Models\Project\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FrontendController extends Controller
{
    public function index()
    {
        $projects = Project::whereNotNull('actual_end_date')->where('budget', '>', 0)->whereHas('expenses')->orderBy('project_name')->get()->map(function ($project) {
            $project->frontend_units = $this->freeUnits($project);

            return $project;
        });

        return view('homepage', compact('projects'));
    }

    public function bookingRequestForm()
    {
        if (! auth()->check()) {
            return redirect()->route('client.register.form')->with('notice', 'ফ্ল্যাট কেনার আগে ক্লায়েন্ট হিসেবে নিবন্ধন করে অনুমোদনের অপেক্ষা করুন।');
        }
        abort_unless((int) auth()->user()->role_id === 8, 403);
        if ((int) auth()->user()->status !== 1) {
            return view('frontend.client-pending');
        }
        $client = Client::where('user_id', auth()->id())->where('status', 1)->firstOrFail();

        $projects = Project::with(['manager', 'designer'])->whereNotNull('actual_end_date')->where('budget', '>', 0)->whereHas('expenses')->orderBy('project_name')->get()->map(function ($project) {
            $project->frontend_units = $this->freeUnits($project);

            return $project;
        })->filter(fn ($project) => count($project->frontend_units) > 0)->values();
        $selectedProjectId = request()->query('project_id');

        return view('frontend.booking-request', compact('projects', 'client', 'selectedProjectId'));
    }

    public function clientRegisterForm()
    {
        return view('frontend.client-register');
    }

    public function clientRegister(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $password = $data['password'];
        unset($data['address'], $data['password'], $data['password_confirmation']);
        $data['role_id'] = 8;
        $data['status'] = 0;
        $data['password_hash'] = Hash::make($password);
        User::create($data);

        return redirect()->route('login')->with('registration_pending', 'নিবন্ধন জমা হয়েছে। Admin অনুমোদন করলে আপনি লগইন করে ফ্ল্যাট বাছতে পারবেন।');
    }

    private function freeUnits(Project $project): array
    {
        $reserved = BookingRequest::where('project_id', $project->id)
            ->whereIn('status', [BookingRequest::PENDING_MANAGER, BookingRequest::PENDING_ACCOUNTANT, BookingRequest::APPROVED])
            ->pluck('unit_label')->map(fn ($label) => mb_strtolower($label));
        $booked = Booking::where('project_id', $project->id)->pluck('unit_label')->map(fn ($label) => mb_strtolower($label));

        return collect($project->listedUnits())
            ->reject(fn ($unit) => $reserved->contains(mb_strtolower($unit['label'])) || $booked->contains(mb_strtolower($unit['label'])))
            ->values()->all();
    }

    public function submitBookingRequest(Request $request)
    {
        abort_unless(auth()->check() && (int) auth()->user()->role_id === 8 && (int) auth()->user()->status === 1, 403);
        $client = Client::where('user_id', auth()->id())->where('status', 1)->firstOrFail();
        $data = BookingRequest::validatedData($request);

        $booking = DB::transaction(function () use ($data, $client) {
            $project = Project::whereKey($data['project_id'])->lockForUpdate()->firstOrFail();
            if (! $project->actual_end_date || (float) $project->budget <= 0 || ! $project->expenses()->exists()) {
                throw ValidationException::withMessages(['project_id' => 'Budget, অন্তত একটি expense, এবং completion date থাকা project-এ অনুরোধ করা যাবে।']);
            }
            $listedUnit = collect($project->listedUnits())->first(fn ($unit) => strtolower($unit['label']) === strtolower($data['unit_label']));
            if (! $listedUnit || (int) round((float) $listedUnit['price'] * 100) !== (int) round((float) $data['agreed_amount'] * 100)) {
                throw ValidationException::withMessages(['unit_label' => 'নির্বাচিত ফ্ল্যাট বা দাম সঠিক নয়। আবার ফ্ল্যাটটি নির্বাচন করুন।']);
            }
            $data['unit_label'] = $listedUnit['label'];
            $data['agreed_amount'] = $listedUnit['price'];
            if ((int) round((float) $data['initial_payment_amount'] * 100) !== (int) round((float) $listedUnit['price'] * 100)) {
                throw ValidationException::withMessages(['initial_payment_amount' => 'অনুমোদনের জন্য পুরো ফ্ল্যাটের দাম একবারে দিতে হবে।']);
            }
            $unitKey = mb_strtolower($data['unit_label']);
            $unitAlreadyTaken = Booking::where('project_id', $project->id)->pluck('unit_label')
                ->contains(fn ($label) => mb_strtolower($label) === $unitKey);
            $unitAlreadyRequested = BookingRequest::where('project_id', $project->id)
                ->whereIn('status', [BookingRequest::PENDING_MANAGER, BookingRequest::PENDING_ACCOUNTANT, BookingRequest::APPROVED])
                ->pluck('unit_label')->contains(fn ($label) => mb_strtolower($label) === $unitKey);

            if ($unitAlreadyTaken || $unitAlreadyRequested) {
                throw ValidationException::withMessages(['unit_label' => 'This flat/unit is already booked or awaiting approval.']);
            }

            return BookingRequest::create([
                'request_no' => 'BR-'.now()->format('ymd').'-'.Str::upper(Str::random(6)),
                'project_id' => $project->id,
                'client_id' => $client->id,
                'unit_label' => $data['unit_label'],
                'agreed_amount' => $data['agreed_amount'],
                'initial_payment_amount' => $data['initial_payment_amount'],
                'payment_method' => $data['payment_method'],
                'payment_reference' => $data['payment_reference'] ?? null,
                'client_message' => $data['client_message'] ?? null,
                'status' => BookingRequest::PENDING_MANAGER,
            ]);
        });

        return redirect()->route('booking.request.status.form')->with('request_received', [
            'request_no' => $booking->request_no,
            'email' => $booking->client->email,
            'project' => $booking->project->project_name,
            'unit' => $booking->unit_label,
            'agreed_amount' => $booking->agreed_amount,
            'initial_payment_amount' => $booking->initial_payment_amount,
        ]);
    }

    public function requestStatusForm()
    {
        return view('frontend.request-status');
    }

    public function requestStatus(Request $request)
    {
        $data = $request->validate(['phone' => ['required', 'string', 'max:20'], 'email' => ['required', 'email', 'max:100']]);
        $bookingRequests = BookingRequest::with('project', 'client')->whereHas('client', fn ($query) => $query->where('email', $data['email'])->where('phone', $data['phone']))->latest()->get();

        if ($bookingRequests->isEmpty()) {
            return back()->withErrors(['email' => 'এই ফোন ও ইমেইলে কোনো অনুরোধ পাওয়া যায়নি।'])->withInput();
        }

        return view('frontend.request-status', compact('bookingRequests'));
    }
}
