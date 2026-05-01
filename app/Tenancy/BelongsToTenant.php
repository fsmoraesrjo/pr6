<?php

namespace App\Tenancy;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $request = request();
            if ($request && $request->is('admin*')) {
                return;
            }

            $manager = app(TenantManager::class);
            $tenant = $manager->current();

            if (! $tenant) {
                return;
            }

            if ($manager->isPortal() && property_exists($builder->getModel(), 'crossTenantOnPortal') && $builder->getModel()::$crossTenantOnPortal) {
                return;
            }

            $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenant->id);
        });

        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $tenant = app(TenantManager::class)->current();
                if ($tenant) {
                    $model->tenant_id = $tenant->id;
                }
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeForTenant(Builder $query, Tenant|int|null $tenant): Builder
    {
        $id = $tenant instanceof Tenant ? $tenant->id : $tenant;
        return $query->withoutGlobalScope('tenant')->where('tenant_id', $id);
    }

    public function scopeAcrossTenants(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }
}
