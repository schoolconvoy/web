<?php

namespace App\Multitenancy;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class DomainTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?Tenant
    {
        $host = $request->getHost();

        // Check for a subdomain
        $parts = explode('.', $host);
        if (count($parts) > 2) {
            $subdomain = $parts[0];
            return Tenant::query()
                ->where('subdomain', $subdomain)
                ->first();
        }

        // Check for a custom domain
        return Tenant::query()
            ->where('domain', $host)
            ->first();
    }
}
