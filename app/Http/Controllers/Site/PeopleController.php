<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\OrgUnit;
use App\Models\TeamMember;
use App\Tenancy\TenantManager;

class PeopleController extends Controller
{
    public function index(TenantManager $manager)
    {
        $isPortal = $manager->isPortal();

        $unitsQuery = $isPortal
            ? OrgUnit::query()->acrossTenants()->with(['tenant'])
            : OrgUnit::query();

        $units = $unitsQuery->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->groupBy('tenant_id');

        $membersQuery = $isPortal
            ? TeamMember::query()->acrossTenants()->with('tenant', 'orgUnit')
            : TeamMember::query()->with('orgUnit');

        $members = $membersQuery->where('is_active', true)
            ->orderByDesc('is_head')
            ->orderBy('order')
            ->get();

        $unitsById = $unitsQuery->where('is_active', true)->get()->keyBy('id');

        $byUnit = $members->groupBy(fn ($m) => $m->org_unit_id ?? 0);

        return view('site.people.index', compact('byUnit', 'unitsById', 'isPortal'));
    }

    public function chart(TenantManager $manager)
    {
        $tenantId = $manager->current()?->id;

        $query = $manager->isPortal()
            ? OrgUnit::query()->acrossTenants()->with(['tenant', 'members' => fn ($q) => $q->where('is_active', true)->orderByDesc('is_head')])
            : OrgUnit::query()->with(['members' => fn ($q) => $q->where('is_active', true)->orderByDesc('is_head')]);

        $units = $query->where('is_active', true)
            ->orderBy('order')
            ->get();

        $byParent = $units->groupBy(fn ($u) => $u->parent_id ?: 'root');

        return view('site.people.chart', [
            'roots' => $byParent->get('root', collect()),
            'byParent' => $byParent,
            'tenantId' => $tenantId,
        ]);
    }
}
