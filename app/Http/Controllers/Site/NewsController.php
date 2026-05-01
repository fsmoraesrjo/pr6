<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Tenancy\TenantManager;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request, TenantManager $manager)
    {
        $query = $manager->isPortal()
            ? News::query()->acrossTenants()->with('tenant')
            : News::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $news = $query->published()->orderByDesc('published_at')->paginate(12)->withQueryString();

        return view('site.news.index', compact('news'));
    }

    public function show(string $slug, TenantManager $manager)
    {
        $query = $manager->isPortal()
            ? News::query()->acrossTenants()->with('tenant')
            : News::query();

        $item = $query->where('slug', $slug)->firstOrFail();
        $item->increment('views_count');

        $related = News::query()
            ->forTenant($item->tenant_id)
            ->where('id', '!=', $item->id)
            ->published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('site.news.show', compact('item', 'related'));
    }
}
