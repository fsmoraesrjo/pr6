<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $rootDev = config('pr6.root_domain');
        $rootProd = config('pr6.root_domain_prod');

        $tenants = [
            [
                'slug' => 'pr6',
                'short_name' => 'PR-6',
                'full_name' => 'Pró-Reitoria de Planejamento e Gestão',
                'tagline' => 'Planejar. Gerir. Transformar.',
                'description' => 'A Pró-Reitoria de Planejamento e Gestão articula tecnologia, infraestrutura, planejamento e sustentabilidade para sustentar a missão da UERJ.',
                'domain_dev' => $rootDev,
                'domain_prod' => $rootProd,
                'accent_color' => '#B92828',
                'accent_soft_color' => '#FCE4E5',
                'accent_deep_color' => '#8E1B1B',
                'icon' => 'building-library',
                'is_root' => true,
                'order' => 0,
            ],
            [
                'slug' => 'dirtec',
                'short_name' => 'DIRTEC',
                'full_name' => 'Diretoria de Tecnologia da Informação e Comunicação',
                'tagline' => 'Tecnologia que conecta a UERJ',
                'description' => 'Sistemas, redes, suporte e inovação digital ao serviço de toda a comunidade universitária.',
                'domain_dev' => 'dirtec.' . $rootDev,
                'domain_prod' => 'dirtec.' . $rootProd,
                'accent_color' => '#2563EB',
                'accent_soft_color' => '#DBEAFE',
                'accent_deep_color' => '#1E3A8A',
                'icon' => 'cpu-chip',
                'is_root' => false,
                'order' => 1,
            ],
            [
                'slug' => 'dirgis',
                'short_name' => 'DIRGIS',
                'full_name' => 'Diretoria de Gestão da Infraestrutura e Serviços',
                'tagline' => 'Infraestrutura e serviços para a vida universitária',
                'description' => 'Manutenção, serviços gerais e gestão dos espaços que sustentam o cotidiano universitário.',
                'domain_dev' => 'dirgis.' . $rootDev,
                'domain_prod' => 'dirgis.' . $rootProd,
                'accent_color' => '#0E7490',
                'accent_soft_color' => '#CFFAFE',
                'accent_deep_color' => '#155E75',
                'icon' => 'building-office-2',
                'is_root' => false,
                'order' => 2,
            ],
            [
                'slug' => 'dirplag',
                'short_name' => 'DIRPLAG',
                'full_name' => 'Diretoria de Planejamento em Infraestrutura',
                'tagline' => 'Planejamento estratégico para o futuro da UERJ',
                'description' => 'Estratégia de longo prazo, projetos e expansão física dos campi da universidade.',
                'domain_dev' => 'dirplag.' . $rootDev,
                'domain_prod' => 'dirplag.' . $rootProd,
                'accent_color' => '#7C3AED',
                'accent_soft_color' => '#EDE9FE',
                'accent_deep_color' => '#5B21B6',
                'icon' => 'chart-bar-square',
                'is_root' => false,
                'order' => 3,
            ],
            [
                'slug' => 'coomas',
                'short_name' => 'COOMAS',
                'full_name' => 'Coordenação de Meio Ambiente e Sustentabilidade',
                'tagline' => 'Práticas sustentáveis para uma universidade verde',
                'description' => 'Práticas sustentáveis, gestão ambiental e ESG em uma universidade pública de ponta.',
                'domain_dev' => 'coomas.' . $rootDev,
                'domain_prod' => 'coomas.' . $rootProd,
                'accent_color' => '#15803D',
                'accent_soft_color' => '#DCFCE7',
                'accent_deep_color' => '#14532D',
                'icon' => 'leaf',
                'is_root' => false,
                'order' => 4,
            ],
        ];

        foreach ($tenants as $data) {
            Tenant::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
