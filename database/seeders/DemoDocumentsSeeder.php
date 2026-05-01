<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentVersion;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DemoDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all()->keyBy('slug');

        $categories = [
            'pr6' => ['Planejamento', 'Relatórios anuais', 'Atos normativos'],
            'dirtec' => ['Políticas de TI', 'Manuais técnicos'],
            'dirgis' => ['Manuais', 'Procedimentos'],
            'dirplag' => ['Estudos', 'Projetos'],
            'coomas' => ['Boas práticas', 'Relatórios ambientais'],
        ];

        foreach ($categories as $tenantSlug => $cats) {
            $tenant = $tenants[$tenantSlug] ?? null;
            if (! $tenant) continue;
            foreach ($cats as $name) {
                DocumentCategory::withoutGlobalScopes()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'slug' => Str::slug($name)],
                    ['name' => $name]
                );
            }
        }

        $documents = [
            ['pr6', 'PDI 2026-2030 — Plano de Desenvolvimento Institucional', 'Planejamento',
                'Documento estratégico que orienta as ações da PR-6 e da UERJ para o período de 2026 a 2030.',
                [
                    ['v1.0', 'Versão inicial publicada após consulta pública.', '2026-03-15', false],
                    ['v1.1', 'Revisão após contribuições do conselho universitário. Inclusão de metas de sustentabilidade.', '2026-04-20', true],
                ],
            ],
            ['pr6', 'Relatório Anual de Gestão 2025', 'Relatórios anuais',
                'Prestação de contas das ações da Pró-Reitoria no exercício de 2025.',
                [
                    ['Edição 2025', 'Documento final aprovado pelo conselho.', '2026-02-28', true],
                ],
            ],
            ['dirtec', 'Política de Segurança da Informação', 'Políticas de TI',
                'Diretrizes obrigatórias para o uso seguro dos sistemas e dados da UERJ.',
                [
                    ['v2.0', 'Atualização para conformidade com LGPD e ISO 27001.', '2026-01-10', true],
                ],
            ],
        ];

        foreach ($documents as [$tenantSlug, $title, $catName, $description, $versions]) {
            $tenant = $tenants[$tenantSlug] ?? null;
            if (! $tenant) continue;

            $category = DocumentCategory::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->where('name', $catName)
                ->first();

            $document = Document::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'description' => $description,
                    'category_id' => $category?->id,
                    'is_public' => true,
                    'published_at' => Carbon::parse('2026-01-01'),
                ]
            );

            foreach ($versions as [$label, $changelog, $date, $isCurrent]) {
                $version = DocumentVersion::updateOrCreate(
                    ['document_id' => $document->id, 'version_label' => $label],
                    [
                        'changelog' => $changelog,
                        'created_at' => Carbon::parse($date),
                        'updated_at' => Carbon::parse($date),
                        'is_current' => $isCurrent,
                        'file_path' => 'demo/placeholder.pdf',
                        'original_name' => 'documento-' . Str::slug($label) . '.pdf',
                        'size_bytes' => rand(200, 4000) * 1024,
                        'mime_type' => 'application/pdf',
                    ]
                );

                if ($isCurrent) {
                    $document->update(['current_version_id' => $version->id]);
                }
            }
        }
    }
}
