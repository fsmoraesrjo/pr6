<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Indicator;
use App\Tenancy\TenantManager;
use Illuminate\Http\Request;

class IndicatorController extends Controller
{
    public function index(Request $request, TenantManager $manager)
    {
        $query = $manager->isPortal()
            ? Indicator::query()->acrossTenants()->with('tenant', 'values')
            : Indicator::query()->with('values');

        if ($d = $request->query('d')) {
            $query->whereHas('tenant', fn ($q) => $q->where('slug', $d));
        }

        $indicators = $query->where('is_public', true)
            ->orderBy('order')
            ->get()
            ->each(function (Indicator $i) {
                $i->setRelation('values', $i->values->sortBy('recorded_at'));
            });

        return view('site.indicators.index', compact('indicators'));
    }

    public function show(string $slug, TenantManager $manager)
    {
        $query = $manager->isPortal()
            ? Indicator::query()->acrossTenants()->with('tenant', 'values')
            : Indicator::query()->with('values');

        $indicator = $query->where('slug', $slug)->where('is_public', true)->firstOrFail();
        $indicator->setRelation('values', $indicator->values->sortBy('recorded_at'));

        $latest = $indicator->values->last();
        $previous = $indicator->values->slice(-2, 1)->first();

        $variation = null;
        if ($latest && $previous && $previous->value > 0) {
            $variation = (($latest->value - $previous->value) / $previous->value) * 100;
        }

        $relatedQuery = $manager->isPortal()
            ? Indicator::query()->acrossTenants()->with('tenant', 'values')
            : Indicator::query()->with('values');
        $related = $relatedQuery->where('id', '!=', $indicator->id)
            ->where('tenant_id', $indicator->tenant_id)
            ->where('is_public', true)
            ->limit(4)
            ->get();

        return view('site.indicators.show', compact('indicator', 'latest', 'previous', 'variation', 'related'));
    }
}
