<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
    public function index()
    {
        $records = Staff::orderByDesc('id')->paginate(10);

        return view('Backend.staffManagement.index', compact('records'));
    }

    public function create()
    {
        return redirect()->route('admin.staffManagement.index');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email|max:100',
            'designation'    => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:100',
            'daily_rate'     => 'nullable|numeric|min:0',
            'status'         => 'required|in:0,1',
        ]);


        // Logged-in user's ID
        $validated['user_id'] = auth()->id();

        Staff::create($validated);

        return redirect()
            ->route('admin.staffManagement.index')
            ->with('success', 'Staff added successfully.');
    }


    public function edit(Staff $staff)
    {
        return redirect()->route('admin.staffManagement.index');
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email|max:100',
            'designation'    => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:100',
            'daily_rate'     => 'nullable|numeric|min:0',
            'status'         => 'required|in:0,1',
        ]);

        $staff->update($validated);

        return redirect()
            ->route('admin.staffManagement.index')
            ->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        try {
            $staff->delete();
            return redirect()
                ->route('admin.staffManagement.index')
                ->with('success', 'Staff deleted successfully.');
        } catch (QueryException $e) {
            Log::error('Staff deletion failed', ['staff_id' => $staff->id, 'error' => $e->getMessage(),]);
            return redirect()
                ->route('admin.staffManagement.index')
                ->with('error', 'Staff cannot be deleted because related records exist.');
        }
    }
}
