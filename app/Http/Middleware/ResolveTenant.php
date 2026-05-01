<?php

namespace App\Http\Middleware;

use App\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(protected TenantManager $manager)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = null;

        if ($request->has('tenant') && app()->environment(['local', 'development'])) {
            $tenant = \App\Models\Tenant::where('slug', $request->query('tenant'))
                ->where('is_active', true)->first();
        }

        if (! $tenant) {
            $tenant = $this->manager->resolveByHost($request->getHost());
        }

        if (! $tenant) {
            abort(404, 'Tenant não encontrado.');
        }

        $this->manager->set($tenant);

        view()->share('tenant', $tenant);
        view()->share('isPortal', $this->manager->isPortal());

        return $next($request);
    }
}
