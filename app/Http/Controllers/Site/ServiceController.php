<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Tenancy\TenantManager;

class ServiceController extends Controller
{
    public function index(TenantManager $manager)
    {
        if ($manager->isPortal()) {
            abort(404);
        }

        $services = Service::query()->where('is_active', true)->orderBy('order')->get();
        return view('site.services.index', compact('services'));
    }

    public function show(string $slug, TenantManager $manager)
    {
        if ($manager->isPortal()) {
            abort(404);
        }

        $service = Service::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('site.services.show', compact('service'));
    }
}
