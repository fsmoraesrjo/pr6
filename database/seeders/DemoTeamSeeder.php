<?php

namespace Database\Seeders;

use App\Models\OrgUnit;
use App\Models\TeamMember;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoTeamSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all()->keyBy('slug');

        $units = [
            'pr6' => [
                ['Gabinete da Pró-Reitoria', 'proreitoria', null, 1],
                ['Assessoria Técnica', 'gerencia', 'Gabinete da Pró-Reitoria', 2],
                ['Secretaria Executiva', 'gerencia', 'Gabinete da Pró-Reitoria', 3],
            ],
            'dirtec' => [
                ['DIRTEC', 'diretoria', null, 1],
                ['Coordenação de Sistemas', 'coordenacao', 'DIRTEC', 2],
                ['Coordenação de Infraestrutura de Redes', 'coordenacao', 'DIRTEC', 3],
                ['Coordenação de Suporte e Atendimento', 'coordenacao', 'DIRTEC', 4],
            ],
            'dirgis' => [
                ['DIRGIS', 'diretoria', null, 1],
                ['Coordenação de Manutenção', 'coordenacao', 'DIRGIS', 2],
                ['Coordenação de Serviços Gerais', 'coordenacao', 'DIRGIS', 3],
            ],
            'dirplag' => [
                ['DIRPLAG', 'diretoria', null, 1],
                ['Coordenação de Projetos', 'coordenacao', 'DIRPLAG', 2],
                ['Coordenação de Planejamento Estratégico', 'coordenacao', 'DIRPLAG', 3],
            ],
            'coomas' => [
                ['COOMAS', 'coordenacao', null, 1],
                ['Núcleo de Sustentabilidade', 'setor', 'COOMAS', 2],
            ],
        ];

        $unitMap = [];

        foreach ($units as $slug => $list) {
            $tenant = $tenants[$slug] ?? null;
            if (! $tenant) continue;

            foreach ($list as [$name, $type, $parent, $order]) {
                $parentId = null;
                if ($parent) {
                    $parentId = $unitMap[$tenant->id . '|' . $parent] ?? null;
                }

                $unit = OrgUnit::withoutGlobalScopes()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'slug' => Str::slug($name)],
                    [
                        'name' => $name,
                        'type' => $type,
                        'parent_id' => $parentId,
                        'order' => $order,
                        'is_active' => true,
                        'description' => null,
                    ]
                );

                $unitMap[$tenant->id . '|' . $name] = $unit->id;
            }
        }

        $members = [
            // PR-6
            ['pr6', 'Gabinete da Pró-Reitoria', 'Maria Helena Soares', 'Pró-Reitora de Planejamento e Gestão', 'mhsoares@uerj.br', '(21) 2334-0101', true, 1],
            ['pr6', 'Assessoria Técnica', 'Ricardo Mendes Lima', 'Assessor Técnico-Chefe', 'rmendes@uerj.br', '(21) 2334-0102', true, 1],
            ['pr6', 'Secretaria Executiva', 'Carla Vieira Souza', 'Secretária Executiva', 'cvsouza@uerj.br', '(21) 2334-0103', true, 1],
            // DIRTEC
            ['dirtec', 'DIRTEC', 'Fábio Moraes', 'Diretor de Tecnologia da Informação e Comunicação', 'fmoraes@uerj.br', '(21) 2334-0200', true, 1],
            ['dirtec', 'Coordenação de Sistemas', 'André Costa Pereira', 'Coordenador de Sistemas', 'acpereira@uerj.br', '(21) 2334-0210', true, 1],
            ['dirtec', 'Coordenação de Infraestrutura de Redes', 'Juliana Almeida Reis', 'Coordenadora de Infraestrutura', 'jareis@uerj.br', '(21) 2334-0220', true, 1],
            ['dirtec', 'Coordenação de Suporte e Atendimento', 'Marcos Rodrigues Lopes', 'Coordenador de Suporte', 'mrlopes@uerj.br', '(21) 2334-0230', true, 1],
            // DIRGIS
            ['dirgis', 'DIRGIS', 'Paulo Henrique Tavares', 'Diretor de Gestão de Infraestrutura e Serviços', 'phtavares@uerj.br', '(21) 2334-0300', true, 1],
            ['dirgis', 'Coordenação de Manutenção', 'Renata Lopes Ferreira', 'Coordenadora de Manutenção', 'rlferreira@uerj.br', '(21) 2334-0310', true, 1],
            ['dirgis', 'Coordenação de Serviços Gerais', 'José Antônio Barbosa', 'Coordenador de Serviços Gerais', 'jabarbosa@uerj.br', '(21) 2334-0320', true, 1],
            // DIRPLAG
            ['dirplag', 'DIRPLAG', 'Fernanda Caldas Ribeiro', 'Diretora de Planejamento em Infraestrutura', 'fcribeiro@uerj.br', '(21) 2334-0400', true, 1],
            ['dirplag', 'Coordenação de Projetos', 'Bruno Souza Nogueira', 'Coordenador de Projetos', 'bsnogueira@uerj.br', '(21) 2334-0410', true, 1],
            ['dirplag', 'Coordenação de Planejamento Estratégico', 'Patrícia Gomes Martins', 'Coordenadora de Planejamento', 'pgmartins@uerj.br', '(21) 2334-0420', true, 1],
            // COOMAS
            ['coomas', 'COOMAS', 'Lucas Matheus Andrade', 'Coordenador de Meio Ambiente e Sustentabilidade', 'lmandrade@uerj.br', '(21) 2334-0500', true, 1],
            ['coomas', 'Núcleo de Sustentabilidade', 'Ana Carolina Silva', 'Analista Ambiental', 'acsilva@uerj.br', '(21) 2334-0510', false, 1],
        ];

        foreach ($members as [$slug, $unitName, $name, $role, $email, $phone, $isHead, $order]) {
            $tenant = $tenants[$slug] ?? null;
            if (! $tenant) continue;

            $unitId = $unitMap[$tenant->id . '|' . $unitName] ?? null;

            TeamMember::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $name],
                [
                    'org_unit_id' => $unitId,
                    'role_title' => $role,
                    'email' => $email,
                    'phone' => $phone,
                    'is_head' => $isHead,
                    'is_active' => true,
                    'order' => $order,
                ]
            );
        }
    }
}
