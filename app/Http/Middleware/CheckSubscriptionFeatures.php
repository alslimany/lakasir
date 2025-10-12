<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionFeatures
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = tenancy()->tenant;

        if (!$tenant) {
            return $next($request);
        }

        // Check if tenant can use the feature
        if (!$tenant->canUseFeature($feature)) {
            return response()->json([
                'message' => 'This feature is not available in your current plan. Please upgrade.',
            ], 403);
        }

        return $next($request);
    }
}
