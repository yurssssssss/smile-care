<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DentistMiddleware
{
    /**
     * Only allow users with role = 'dentist' to pass through.
     * Everyone else gets redirected to their correct page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'dentist') {
            return match (Auth::user()->role) {
                'admin'  => redirect()->route('admin.dashboard'),
                default  => redirect()->route('patient.home'),
            };
        }

        return $next($request);
    }
}