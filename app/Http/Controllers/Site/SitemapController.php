<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Event;
use App\Models\Indicator;
use App\Models\News;
use App\Models\Service;
use App\Tenancy\TenantManager;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function __invoke(TenantManager $manager)
    {
        $tenant = $manager->current();
        // Em produção: https://pr6.lumislabs.com.br (sem path).
        // Em dev: usar request->getSchemeAndHttpHost preserva 127.0.0.1:5183.
        $baseUrl = app()->environment('production')
            ? 'https://' . ($tenant->domain_prod ?: $tenant->domain_dev)
            : request()->getSchemeAndHttpHost();
        $now = now()->toAtomString();

        $urls = [];

        // Páginas estáticas
        $static = [
            ['/', 'daily', '1.0'],
            ['/sobre', 'monthly', '0.7'],
            ['/noticias', 'daily', '0.9'],
            ['/documentos', 'weekly', '0.9'],
            ['/agenda', 'daily', '0.8'],
            ['/indicadores', 'weekly', '0.8'],
            ['/pessoas', 'monthly', '0.6'],
            ['/organograma', 'monthly', '0.5'],
            ['/contato', 'monthly', '0.5'],
            ['/privacidade', 'yearly', '0.4'],
            ['/lgpd', 'monthly', '0.4'],
            ['/acessibilidade', 'yearly', '0.3'],
        ];

        if (! $manager->isPortal()) {
            $static[] = ['/servicos', 'monthly', '0.7'];
        }

        foreach ($static as [$path, $freq, $priority]) {
            $urls[] = compact('path', 'freq', 'priority') + ['lastmod' => $now];
        }

        // Notícias publicadas
        $newsQuery = $manager->isPortal()
            ? News::query()->acrossTenants()
            : News::query();
        $newsQuery->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(500)
            ->each(function (News $n) use (&$urls) {
                $urls[] = [
                    'path' => '/noticias/' . $n->slug,
                    'freq' => 'monthly',
                    'priority' => '0.7',
                    'lastmod' => Carbon::parse($n->updated_at ?? $n->published_at)->toAtomString(),
                ];
            });

        // Documentos públicos
        $docQuery = $manager->isPortal()
            ? Document::query()->acrossTenants()
            : Document::query();
        $docQuery->where('is_public', true)
            ->orderByDesc('published_at')
            ->limit(500)
            ->each(function (Document $d) use (&$urls) {
                $urls[] = [
                    'path' => '/documentos/' . $d->slug,
                    'freq' => 'monthly',
                    'priority' => '0.7',
                    'lastmod' => Carbon::parse($d->updated_at ?? $d->published_at ?? $d->created_at)->toAtomString(),
                ];
            });

        // Indicadores públicos
        $indQuery = $manager->isPortal()
            ? Indicator::query()->acrossTenants()
            : Indicator::query();
        $indQuery->where('is_public', true)
            ->limit(200)
            ->each(function (Indicator $i) use (&$urls) {
                $urls[] = [
                    'path' => '/indicadores/' . $i->slug,
                    'freq' => 'weekly',
                    'priority' => '0.6',
                    'lastmod' => Carbon::parse($i->updated_at)->toAtomString(),
                ];
            });

        // Serviços (apenas verticais)
        if (! $manager->isPortal()) {
            Service::query()
                ->where('is_active', true)
                ->limit(200)
                ->each(function (Service $s) use (&$urls) {
                    $urls[] = [
                        'path' => '/servicos/' . $s->slug,
                        'freq' => 'monthly',
                        'priority' => '0.6',
                        'lastmod' => Carbon::parse($s->updated_at)->toAtomString(),
                    ];
                });
        }

        // Eventos futuros públicos
        $evQuery = $manager->isPortal()
            ? Event::query()->acrossTenants()
            : Event::query();
        $evQuery->where('is_public', true)
            ->where('starts_at', '>=', now()->subMonth())
            ->orderBy('starts_at')
            ->limit(200)
            ->each(function (Event $e) use (&$urls) {
                $urls[] = [
                    'path' => '/agenda#evento-' . $e->id,
                    'freq' => 'weekly',
                    'priority' => '0.5',
                    'lastmod' => Carbon::parse($e->updated_at)->toAtomString(),
                ];
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($baseUrl . $u['path']) . "</loc>\n";
            $xml .= "    <lastmod>" . $u['lastmod'] . "</lastmod>\n";
            $xml .= "    <changefreq>" . $u['freq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $u['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function robots(TenantManager $manager)
    {
        $tenant = $manager->current();
        $baseUrl = app()->environment('production')
            ? 'https://' . ($tenant->domain_prod ?: $tenant->domain_dev)
            : request()->getSchemeAndHttpHost();

        $body = "User-agent: *\n";
        $body .= "Allow: /\n";
        $body .= "Disallow: /admin\n";
        $body .= "Disallow: /lgpd/consent\n";
        $body .= "\n";
        $body .= "Sitemap: " . $baseUrl . "/sitemap.xml\n";

        return response($body, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
