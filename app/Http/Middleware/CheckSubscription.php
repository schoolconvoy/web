<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $tenant = Tenant::find(Auth::user()->school_id);

            // Skip check for super admin
            if (Auth::user()->isSuperAdmin()) {
                return $next($request);
            }

            // If no tenant or no plan, redirect to subscription page
            if (!$tenant || !$tenant->plan) {
                return redirect()->route('filament.admin.pages.subscription')
                    ->with('warning', 'Please subscribe to a plan to continue using the platform.');
            }

            // Check if subscription is active
            if (!$tenant->subscription || !$tenant->subscription->isActive()) {
                return redirect()->route('filament.admin.pages.subscription')
                    ->with('warning', 'Your subscription has expired. Please renew to continue using the platform.');
            }
        }

        return $next($request);
    }
}
