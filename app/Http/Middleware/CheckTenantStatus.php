<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenancy()->tenant;

        if (!$tenant) {
            return $next($request);
        }

        // Check if tenant is active
        if (!$tenant->is_active) {
            return response()->view('errors.tenant-suspended', [], 403);
        }

        // Allow access to billing page even if subscription expired
        if ($request->routeIs('filament.tenant.pages.billing')) {
            return $next($request);
        }

        // Check if tenant has active subscription or trial
        if (!$tenant->hasActiveSubscription()) {
            return redirect()->route('filament.tenant.pages.billing');
        }

        return $next($request);
    }
}
