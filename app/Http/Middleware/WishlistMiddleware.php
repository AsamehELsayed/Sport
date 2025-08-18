<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WishlistMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug logging
        \Log::info('WishlistMiddleware called', [
            'url' => $request->url(),
            'method' => $request->method(),
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'expects_json' => $request->expectsJson()
        ]);

        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Authentication required to access wishlist functionality',
                    'redirect' => '/login'
                ], 401);
            }

            return redirect()->route('customer.login')->with('error', 'Please login to access your wishlist.');
        }

        return $next($request);
    }
}
