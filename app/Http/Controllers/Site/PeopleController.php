<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\OrgUnit;
use App\Models\TeamMember;
use App\Models\Tenant;
use App\Tenancy\TenantManager;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    public function index(Request $request, TenantManager $manager)
    {
        $isPortal = $manager->isPortal();
        $q = trim((string) $request->query('q', ''));
        $diretoria = $request->query('d');
        $unidade = $request->query('u');

        $query = $isPortal
            ? TeamMember::query()->acrossTenants()->with('tenant', 'orgUnit')
            : TeamMember::query()->with('orgUnit', 'tenant');

        $query->where('is_active', true);

        if ($q !== '' && mb_strlen($q) >= 2) {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                  ->orWhere('role_title', 'like', $like)
                  ->orWhere('email', 'like', $like);
            });
        }

        if ($diretoria) {
            $query->whereHas('tenant', fn ($t) => $t->where('slug', $diretoria));
        }

        if ($unidade) {
            $query->whereHas('orgUnit', fn ($u) => $u->where('slug', $unidade));
        }

        $members = $query
            ->orderByDesc('is_head')
            ->orderBy('order')
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        $tenants = $isPortal
            ? Tenant::where('is_active', true)->orderBy('order')->get()
            : Tenant::where('id', $manager->id())->get();

        $unitsQuery = $isPortal
            ? OrgUnit::query()->acrossTenants()
            : OrgUnit::query();
        if ($diretoria) {
            $unitsQuery->whereHas('tenant', fn ($t) => $t->where('slug', $diretoria));
        }
        $units = $unitsQuery->where('is_active', true)->orderBy('order')->get();

        $totalActive = TeamMember::query()
            ->{$isPortal ? 'acrossTenants' : 'where'}(...($isPortal ? [] : ['is_active', true]))
            ->where('is_active', true)
            ->count();

        return view('site.people.index', compact('members', 'tenants', 'units', 'q', 'diretoria', 'unidade', 'isPortal', 'totalActive'));
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
