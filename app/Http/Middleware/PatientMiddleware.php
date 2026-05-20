<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PatientMiddleware
{
    /**
     * Only allow users with role = 'patient' to pass through.
     * Everyone else gets redirected to their correct page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'patient') {
            return match (Auth::user()->role) {
                'admin'   => redirect()->route('admin.dashboard'),
                'dentist' => redirect()->route('dentist.dashboard'),
                default   => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}