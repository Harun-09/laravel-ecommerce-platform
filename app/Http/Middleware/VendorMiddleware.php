<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Admin accounts are redirected to the admin dashboard.');
        }

        if (!$user->hasRole('vendor')) {
            abort(403, 'Unauthorized access');
        }

        $vendor = $user->vendor;

        if (!$vendor || !$vendor->isApproved()) {
            return redirect()->route('vendor.pending');
        }

        if (Gate::forUser($user)->denies('access-vendor-panel', $vendor)) {
            abort(403, 'Unauthorized vendor access');
        }

        $request->attributes->set('vendor_id', $vendor->id);

        return $next($request);
    }
}
