<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Only allow users with role = 'admin' to pass through.
     * Everyone else gets redirected to their correct page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'admin') {
            return match (Auth::user()->role) {
                'dentist' => redirect()->route('dentist.dashboard'),
                default   => redirect()->route('patient.home'),
            };
        }

        return $next($request);
    }
}