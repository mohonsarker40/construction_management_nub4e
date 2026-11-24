<?php

namespace App\Http\Controllers\Backend\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking\Booking;
use App\Models\Booking\BookingRequest;
use App\Models\Client\Client;

class ClientDashboardController extends Controller
{
    public function index()
    {
        abort_unless((int) auth()->user()->role_id === 8, 403);

        $client = Client::where('user_id', auth()->id())
            ->where('status', 1)
            ->firstOrFail();
        $requests = BookingRequest::with(['project', 'booking'])
            ->where('client_id', $client->id)
            ->latest()
            ->get();
        $bookings = Booking::with(['project', 'payments' => fn ($query) => $query->orderBy('payment_date')->orderBy('id')])
            ->where('client_id', $client->id)
            ->where('status', Booking::BOOKED)
            ->orderByDesc('booking_date')
            ->get();

        return view('Backend.client-dashboard', compact('client', 'requests', 'bookings'));
    }
}
