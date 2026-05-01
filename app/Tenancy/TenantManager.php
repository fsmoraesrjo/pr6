<?php

namespace App\Tenancy;

use App\Models\Tenant;

class TenantManager
{
    protected ?Tenant $current = null;
    protected bool $isPortal = false;

    public function set(Tenant $tenant): void
    {
        $this->current = $tenant;
        $this->isPortal = (bool) $tenant->is_root;
    }

    public function current(): ?Tenant
    {
        return $this->current;
    }

    public function id(): ?int
    {
        return $this->current?->id;
    }

    public function isPortal(): bool
    {
        return $this->isPortal;
    }

    public function isVertical(): bool
    {
        return $this->current && ! $this->current->is_root;
    }

    public function resolveByHost(string $host): ?Tenant
    {
        $host = strtolower(preg_replace('/:\d+$/', '', $host));

        $tenant = Tenant::query()
            ->where(function ($q) use ($host) {
                $q->where('domain_dev', $host)
                  ->orWhere('domain_prod', $host);
            })
            ->where('is_active', true)
            ->first();

        if ($tenant) {
            return $tenant;
        }

        $rootDomains = [
            config('pr6.root_domain'),
            config('pr6.root_domain_prod'),
        ];

        foreach ($rootDomains as $rootDomain) {
            if (! $rootDomain) continue;
            if ($host === $rootDomain) {
                return Tenant::where('is_root', true)->where('is_active', true)->first();
            }
            $prefix = '.' . $rootDomain;
            if (str_ends_with($host, $prefix)) {
                $slug = substr($host, 0, -strlen($prefix));
                $tenant = Tenant::where('slug', $slug)->where('is_active', true)->first();
                if ($tenant) return $tenant;
            }
        }

        return Tenant::where('is_root', true)->where('is_active', true)->first();
    }
}
