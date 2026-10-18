<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $records = Client::orderByDesc('id')->paginate(10);

        return view('Backend.clients.index', compact('records'));
    }

    public function create()
    {
        $client = new Client();

        return view('Backend.clients.create', compact('client'));
    }

    public function store(Request $request)
    {
        Client::create(Client::validatedData($request));

        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
    }

    public function edit(Client $client)
    {
        return view('Backend.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $client->update(Client::validatedData($request));

        return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        try {
            $client->delete();
        } catch (QueryException) {
            return redirect()->route('admin.clients.index')
                ->with('error', 'This client is linked to existing projects and cannot be deleted.');
        }

        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
    }
}
