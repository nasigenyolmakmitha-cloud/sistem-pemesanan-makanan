<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') or middleware('role:kasir')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect('/admin/login');
        }

        if (auth()->user()->role !== $role) {
            // Redirect to appropriate dashboard based on their actual role
            if (auth()->user()->role === 'admin') {
                return redirect('/admin/dashboard');
            }
            return redirect('/kasir/dashboard');
        }

        return $next($request);
    }
}
