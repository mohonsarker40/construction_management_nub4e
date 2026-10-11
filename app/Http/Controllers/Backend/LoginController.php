<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{

    public function index()
    {
        return view('Backend/Auth/login');
    }


    public function doLogin(Request $request){ // for dashboard
        $credentials = [
            'email' =>  $request->input('email'),
            'password' =>  $request->input('password'),
        ];


        $login = Auth::attempt($credentials);
        if($login){
            return redirect()->route('dashboard');
        }else{
            Session::flash('failed', 'Login Failed');
            return redirect()->back();
        }


    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
