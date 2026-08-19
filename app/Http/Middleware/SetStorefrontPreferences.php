<?php

namespace App\Http\Middleware;

use App\Support\StorefrontPreferences;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetStorefrontPreferences
{
    public function handle(Request $request, Closure $next): Response
    {
        $context = StorefrontPreferences::applyToRequest($request);

        view()->share('storefront', $context);

        return $next($request);
    }
}
