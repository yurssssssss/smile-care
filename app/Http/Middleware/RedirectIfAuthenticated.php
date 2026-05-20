<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * If the user is already logged in and tries to visit /login or /register,
     * redirect them to their correct dashboard instead.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return match (Auth::user()->role) {
                'admin'   => redirect()->route('admin.dashboard'),
                'dentist' => redirect()->route('dentist.dashboard'),
                default   => redirect()->route('patient.home'),
            };
        }

        return $next($request);
    }
}