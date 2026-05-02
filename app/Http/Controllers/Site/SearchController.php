<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\News;
use App\Models\Service;
use App\Models\TeamMember;
use App\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function __invoke(Request $request, TenantManager $manager)
    {
        $q = trim((string) $request->query('q', ''));
        $tab = $request->query('tab', 'all');
        $results = [
            'news' => collect(),
            'documents' => collect(),
            'services' => collect(),
            'people' => collect(),
        ];
        $totals = ['news' => 0, 'documents' => 0, 'services' => 0, 'people' => 0];

        if (mb_strlen($q) >= 2) {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
            $isPortal = $manager->isPortal();

            // Notícias
            $newsQuery = $isPortal
                ? News::query()->acrossTenants()->with('tenant')
                : News::query()->with('tenant');
            $newsQuery->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                      ->orWhere('summary', 'like', $like)
                      ->orWhere('body', 'like', $like);
                });
            $totals['news'] = $newsQuery->count();
            $results['news'] = $newsQuery->orderByDesc('published_at')->limit(20)->get();

            // Documentos
            $docQuery = $isPortal
                ? Document::query()->acrossTenants()->with('tenant', 'category')
                : Document::query()->with('category');
            $docQuery->where('is_public', true)
                ->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                      ->orWhere('description', 'like', $like);
                });
            $totals['documents'] = $docQuery->count();
            $results['documents'] = $docQuery->orderByDesc('published_at')->limit(20)->get();

            // Serviços (somente verticais)
            if (!$isPortal) {
                $servQuery = Service::query()->where('is_active', true)
                    ->where(function ($w) use ($like) {
                        $w->where('title', 'like', $like)
                          ->orWhere('summary', 'like', $like)
                          ->orWhere('description', 'like', $like);
                    });
                $totals['services'] = $servQuery->count();
                $results['services'] = $servQuery->orderBy('order')->limit(20)->get();
            }

            // Pessoas
            $peopleQuery = $isPortal
                ? TeamMember::query()->acrossTenants()->with('tenant', 'orgUnit')
                : TeamMember::query()->with('orgUnit');
            $peopleQuery->where('is_active', true)
                ->where(function ($w) use ($like) {
                    $w->where('name', 'like', $like)
                      ->orWhere('role_title', 'like', $like)
                      ->orWhere('bio', 'like', $like);
                });
            $totals['people'] = $peopleQuery->count();
            $results['people'] = $peopleQuery->orderBy('name')->limit(20)->get();
        }

        $totalAll = array_sum($totals);

        return view('site.search.index', compact('q', 'tab', 'results', 'totals', 'totalAll'));
    }
}
