<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminFromFrontend
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->hasAnyRole(['super-admin', 'admin'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin accounts are redirected to the admin dashboard.',
                    'redirect_to' => route('admin.dashboard'),
                ], 403);
            }

            return redirect()
                ->route('admin.dashboard')
                ->with('warning', 'Admin accounts are redirected to the admin dashboard.');
        }

        return $next($request);
    }
}
