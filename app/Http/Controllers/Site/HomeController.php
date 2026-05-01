<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Tenancy\TenantManager;

class HomeController extends Controller
{
    public function __invoke(TenantManager $manager)
    {
        $tenant = $manager->current();

        if ($manager->isPortal()) {
            return view('site.portal.home');
        }

        return view('site.vertical.home');
    }
}
