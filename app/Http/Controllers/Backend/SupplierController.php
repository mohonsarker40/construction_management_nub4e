<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function index()
    {
        $records = Supplier::orderByDesc('id')->paginate(10);

        return view('Backend.suppliers.index', compact('records'));
    }

    public function create()
    {
        return redirect()->route('admin.suppliers.index');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name'      => 'required|string|max:255',
            'contact_person'      => 'required|string|max:255',
            'phone'          => 'required|string|max:30',
            'email'          => 'nullable|email|max:255',
            'city'    => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'gst_number'     => 'nullable|numeric|min:0',
            'status'         => 'required|in:0,1',
        ]);

        // Logged-in user's ID
        $validated['user_id'] = auth()->id();

        Supplier::create($validated);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Suppliers added successfully.');
    }


    public function edit(Supplier $supplier)
    {
        return redirect()->route('admin.suppliers.index');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_name'      => 'required|string|max:255',
            'contact_person'      => 'required|string|max:255',
            'phone'          => 'required|string|max:30',
            'email'          => 'nullable|email|max:255',
            'city'    => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'gst_number'     => 'nullable|numeric|min:0',
            'status'         => 'required|in:0,1',
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Suppliers updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
            return redirect()
                ->route('admin.suppliers.index')
                ->with('success', 'Suppliers deleted successfully.');
        } catch (QueryException $e) {
            Log::error('Suppliers deletion failed', ['supplier_id' => $supplier->id, 'error' => $e->getMessage(),]);
            return redirect()
                ->route('admin.suppliers.index')
                ->with('error', 'Suppliers cannot be deleted because related records exist.');
        }
    }
}
