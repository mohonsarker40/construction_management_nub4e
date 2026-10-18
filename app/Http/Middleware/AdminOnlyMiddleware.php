<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnlyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ((int) $request->user()->role_id !== 1) {
            abort(403, 'Only administrators can manage users and roles.');
        }

        return $next($request);
    }
}
