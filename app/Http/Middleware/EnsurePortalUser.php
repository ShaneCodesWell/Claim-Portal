<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('customer')->check() || Auth::guard('agent')->check()) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}