<?php

namespace App\Http\Controllers;

use App\Models\Frontend;
use Illuminate\Http\Request;

class FrontendController extends Controller
{

    public function index()
    {
        return view('homepage');
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show(Frontend $fronted)
    {
        //
    }


    public function edit(Frontend $fronted)
    {
        //
    }


    public function update(Request $request, Frontend $fronted)
    {
        //
    }


    public function destroy(Frontend $fronted)
    {
        //
    }
}
