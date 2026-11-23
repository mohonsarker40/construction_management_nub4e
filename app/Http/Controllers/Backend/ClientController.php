<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $records = Client::orderByDesc('id')->paginate(10);
        $pendingRegistrations = User::where('role_id', 8)->where('status', 0)->orderBy('created_at')->get();

        return view('Backend.clients.index', compact('records', 'pendingRegistrations'));
    }

    public function edit(Client $client)
    {
        return view('Backend.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:120'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100', 'unique:clients,email,' . $client->id],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:0,1'],
        ]);

        DB::transaction(function () use ($client, $data) {
            $client->update($data);

            if ($client->user) {
                $client->user->update([
                    'full_name' => $data['client_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'status' => (int) $data['status'],
                ]);
            }
        });

        return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        if ($client->bookingRequests()->exists() || $client->bookings()->exists()) {
            return redirect()->route('admin.clients.index')->with('error', 'This client has booking records and cannot be deleted.');
        }

        DB::transaction(function () use ($client) {
            if ($client->user) {
                $client->user->update(['status' => 0]);
            }
            $client->delete();
        });

        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
    }

    public function approveRegistration(User $user)
    {
        abort_unless((int) $user->role_id === 8 && (int) $user->status === 0, 404);

        DB::transaction(function () use ($user) {
            $account = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            abort_unless((int) $account->role_id === 8 && (int) $account->status === 0, 422);
            $client = Client::where('user_id', $account->id)->orWhere('email', $account->email)->lockForUpdate()->first();
            abort_if($client && $client->user_id && (int) $client->user_id !== (int) $account->id, 422, 'এই ইমেইল অন্য একটি ক্লায়েন্ট অ্যাকাউন্টের সঙ্গে যুক্ত।');

            $details = [
                'user_id' => $account->id,
                'client_name' => $account->full_name,
                'contact_person' => $account->full_name,
                'email' => $account->email,
                'phone' => $account->phone ?: 'Not provided',
                'status' => 1,
            ];
            if ($client) $client->update($details);
            else Client::create($details);
            $account->update(['status' => 1]);
        });

        return redirect()->route('admin.clients.index')->with('success', 'ক্লায়েন্ট অ্যাকাউন্ট অনুমোদিত ও সক্রিয় হয়েছে।');
    }
}
