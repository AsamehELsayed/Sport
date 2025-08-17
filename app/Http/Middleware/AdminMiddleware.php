<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // For now, we'll consider all authenticated users as admins
        // In a real app, you'd check for a specific role or permission
        if (!Auth::user()->is_admin ?? true) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
