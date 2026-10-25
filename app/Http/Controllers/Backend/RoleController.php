<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $records = Role::withCount('users')->orderBy('id', 'desc')->paginate(10);

        return view('Backend.roles.index', compact('records'));
    }

    public function create()
    {
        return view('Backend.roles.create');
    }

    public function store(Request $request)
    {
        $data = Role::validatedData($request);

        Role::create($data);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('Backend.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $data = Role::validatedData($request, $role);

        $role->update($data);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ((int) $role->id === 1) {
            return redirect()->route('admin.roles.index')->with('error', 'The Admin role cannot be deleted.');
        }

        if ($role->users()->exists()) {
            return redirect()->route('admin.roles.index')->with('error', 'This role is assigned to users and cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
