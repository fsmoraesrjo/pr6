<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TeamMember;
use App\Tenancy\TenantManager;

class AboutController extends Controller
{
    public function __invoke(TenantManager $manager)
    {
        $verticals = Tenant::where('is_root', false)->where('is_active', true)->orderBy('order')->get();

        if ($manager->isPortal()) {
            $head = TeamMember::query()->acrossTenants()->with('tenant', 'orgUnit')
                ->whereHas('tenant', fn ($q) => $q->where('is_root', true))
                ->where('is_head', true)
                ->where('is_active', true)
                ->first();
        } else {
            $head = TeamMember::query()
                ->where('is_head', true)
                ->where('is_active', true)
                ->whereHas('orgUnit', fn ($q) => $q->whereNull('parent_id'))
                ->with('orgUnit')
                ->first();
        }

        return view('site.about.index', compact('verticals', 'head'));
    }
}
