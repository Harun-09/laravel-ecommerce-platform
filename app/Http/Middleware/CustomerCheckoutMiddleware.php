<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerCheckoutMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->hasRole('customer')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkout and payment are available for customer accounts only.',
                ], 403);
            }

            return redirect()->route('home')
                ->with('warning', 'Checkout and payment are available for customer accounts only.');
        }

        return $next($request);
    }
}
