<?php

return [
    'root_domain' => env('PR6_ROOT_DOMAIN', 'pr6.test'),
    'root_domain_prod' => env('PR6_ROOT_DOMAIN_PROD', 'pr6.uerj.br'),

    'tenants' => [
        'pr6' => [
            'short_name' => 'PR-6',
            'full_name' => 'Pró-Reitoria de Planejamento e Gestão',
            'tagline' => 'Planejar. Gerir. Transformar.',
            'accent_color' => '#B92828',
            'accent_soft_color' => '#FCE4E5',
            'accent_deep_color' => '#8E1B1B',
            'is_root' => true,
        ],
        'dirtec' => [
            'short_name' => 'DIRTEC',
            'full_name' => 'Diretoria de Tecnologia da Informação e Comunicação',
            'tagline' => 'Tecnologia que conecta a UERJ',
            'accent_color' => '#2563EB',
            'accent_soft_color' => '#DBEAFE',
            'accent_deep_color' => '#1E3A8A',
        ],
        'dirgis' => [
            'short_name' => 'DIRGIS',
            'full_name' => 'Diretoria de Gestão da Infraestrutura e Serviços',
            'tagline' => 'Infraestrutura e serviços para a vida universitária',
            'accent_color' => '#0E7490',
            'accent_soft_color' => '#CFFAFE',
            'accent_deep_color' => '#155E75',
        ],
        'dirplag' => [
            'short_name' => 'DIRPLAG',
            'full_name' => 'Diretoria de Planejamento em Infraestrutura',
            'tagline' => 'Planejamento estratégico para o futuro da UERJ',
            'accent_color' => '#7C3AED',
            'accent_soft_color' => '#EDE9FE',
            'accent_deep_color' => '#5B21B6',
        ],
        'coomas' => [
            'short_name' => 'COOMAS',
            'full_name' => 'Coordenação de Meio Ambiente e Sustentabilidade',
            'tagline' => 'Práticas sustentáveis para uma universidade verde',
            'accent_color' => '#15803D',
            'accent_soft_color' => '#DCFCE7',
            'accent_deep_color' => '#14532D',
        ],
    ],
];
