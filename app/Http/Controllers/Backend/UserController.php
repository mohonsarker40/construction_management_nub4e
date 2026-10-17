<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $records = User::with('role')->orderBy('id')->paginate(10);

        return view('Backend.users.index', compact('records'));
    }

    public function create()
    {
        $roles = Role::orderBy('role_name')->get();

        return view('Backend.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = User::validatedData($request);

        unset($data['password']);
        $data['password_hash'] = Hash::make($request->input('password'));
        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('role_name')->get();

        return view('Backend.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = User::validatedData($request, $user);

        if ($user->is(auth()->user()) && ((int) $data['role_id'] !== 1 || (int) $data['status'] !== 1)) {
            return redirect()->back()->withInput()->with('error', 'You cannot remove your own administrator access.');
        }

        if (filled($data['password'] ?? null)) {
            $user->password_hash = Hash::make($data['password']);
        }

        unset($data['password']);
        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->is(auth()->user())) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        try {
            $user->delete();
        } catch (QueryException) {
            return redirect()->route('admin.users.index')->with('error', 'This user is linked to existing records and cannot be deleted.');
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
