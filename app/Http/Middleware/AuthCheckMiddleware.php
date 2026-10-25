<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthCheckMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()){
            return $next($request);
        } else{
            return redirect('/login');
        }
    }
}
