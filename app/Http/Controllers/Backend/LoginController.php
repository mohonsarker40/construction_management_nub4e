<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('Backend/Auth/login');
    }

    public function doLogin(Request $request)
    {
        $credentials = User::validatedCredentials($request);
        $pendingClient = User::where('email', $credentials['email'])
            ->where('role_id', 8)
            ->where('status', 0)
            ->exists();
        if ($pendingClient) {
            return back()->withInput($request->only('email'))
                ->with('pendingApproval', 'আপনার ক্লায়েন্ট অ্যাকাউন্ট Admin-এর অনুমোদনের অপেক্ষায় আছে।');
        }

        if (Auth::attempt($credentials + ['status' => 1])) {
            $request->session()->regenerate();
            if ((int) auth()->user()->role_id === 8) {
                return redirect()->route('client.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return back()->with('failed', 'Invalid email or password');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
