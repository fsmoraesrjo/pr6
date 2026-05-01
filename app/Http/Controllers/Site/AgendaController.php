<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Tenancy\TenantManager;

class AgendaController extends Controller
{
    public function index(TenantManager $manager)
    {
        $query = $manager->isPortal()
            ? Event::query()->acrossTenants()->with('tenant')
            : Event::query();

        $events = $query->public()->orderBy('starts_at')->limit(60)->get();

        return view('site.agenda.index', compact('events'));
    }
}
