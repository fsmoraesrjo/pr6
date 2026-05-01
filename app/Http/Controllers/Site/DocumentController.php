<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentVersion;
use App\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    public function index(Request $request, TenantManager $manager)
    {
        $query = $manager->isPortal()
            ? Document::query()->acrossTenants()->with('tenant', 'category', 'currentVersion')
            : Document::query()->with('category', 'currentVersion');

        if ($search = $request->query('q')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($cat = $request->query('categoria')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $cat));
        }

        $documents = $query->where('is_public', true)->orderByDesc('published_at')->paginate(12)->withQueryString();

        $categories = $manager->isPortal()
            ? DocumentCategory::acrossTenants()->orderBy('name')->get()
            : DocumentCategory::orderBy('name')->get();

        return view('site.documents.index', compact('documents', 'categories'));
    }

    public function show(string $slug, TenantManager $manager)
    {
        $query = $manager->isPortal()
            ? Document::query()->acrossTenants()->with('tenant', 'category', 'versions', 'currentVersion')
            : Document::query()->with('category', 'versions', 'currentVersion');

        $document = $query->where('slug', $slug)->where('is_public', true)->firstOrFail();

        return view('site.documents.show', compact('document'));
    }

    public function download(string $slug, ?int $versionId, TenantManager $manager)
    {
        $query = $manager->isPortal()
            ? Document::query()->acrossTenants()
            : Document::query();

        $document = $query->where('slug', $slug)->where('is_public', true)->firstOrFail();

        $version = $versionId
            ? DocumentVersion::where('document_id', $document->id)->findOrFail($versionId)
            : $document->currentVersion;

        if (! $version || ! $version->file_path) {
            abort(404, 'Arquivo não encontrado.');
        }

        $document->increment('downloads_count');

        $path = storage_path('app/public/' . ltrim($version->file_path, '/'));

        if (! file_exists($path)) {
            abort(404, 'Arquivo não disponível no servidor.');
        }

        return response()->download($path, $version->original_name ?? basename($path));
    }
}
