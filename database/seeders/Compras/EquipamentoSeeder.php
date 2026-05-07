<?php

namespace Database\Seeders\Compras;

use App\Models\Compras\Equipamento;
use Illuminate\Database\Seeder;

class EquipamentoSeeder extends Seeder
{
    public function run(): void
    {
        $itens = [
            // Mobiliario
            ['Mesa de escritório',       'Mobiliário',      'Mesa em L 1,40m x 1,60m', 'MDF 25mm, com gaveteiro'],
            ['Cadeira ergonômica',       'Mobiliário',      'Giratória com regulagens', 'NR-17, 5 rodízios, braços ajustáveis'],
            ['Armário de aço 2 portas',  'Mobiliário',      'Armário alto chapeado',    'Chapa #22, 4 prateleiras'],
            ['Estação de trabalho',      'Mobiliário',      'Bancada modular',          'Tampo 1500x600mm'],

            // Informatica
            ['Notebook',                 'Informática',     'Notebook corporativo i5', '16GB RAM, SSD 512GB, tela 14"'],
            ['Desktop',                  'Informática',     'Workstation i7',           '32GB RAM, SSD 1TB, GPU dedicada'],
            ['Monitor 24"',              'Informática',     'Monitor LED Full HD',      'IPS 24", HDMI/DP'],
            ['Impressora multifuncional','Informática',     'Laser monocromática',      'Rede + duplex'],
            ['No-break',                 'Informática',     '1500VA bivolt',            '6 tomadas, USB management'],
            ['Tablet',                   'Informática',     'Tablet 10" Wi-Fi',         'Para uso em campo'],

            // Audiovisual
            ['Projetor multimídia',      'Audiovisual',     'Full HD 4000 lumens',      'HDMI/USB, foco automático'],
            ['Tela de projeção',         'Audiovisual',     'Retrátil 100" diagonal',   'Manual/elétrica'],
            ['Sistema de videoconferência','Audiovisual',   'Câmera + microfone array', 'USB-C plug-and-play'],

            // Laboratorio
            ['Microscópio óptico',       'Laboratório',     'Trinocular biológico',     'Aumento 40x-1000x'],
            ['Centrífuga de bancada',    'Laboratório',     'Capacidade 12 tubos',      'Até 4000 RPM'],
            ['Estufa de esterilização',  'Laboratório',     'Volume 80L',               'Até 250°C, digital'],
            ['Balança de precisão',      'Laboratório',     '0,001g de resolução',      'Capacidade 220g'],

            // Servicos gerais
            ['Geladeira',                'Serviços Gerais', 'Frost-free 350L',          'Inox, classe A'],
            ['Bebedouro elétrico',       'Serviços Gerais', 'Coluna 3 temperaturas',    'Galão 20L, filtro'],
            ['Forno micro-ondas',        'Serviços Gerais', '32L bivolt',               'Para copa'],
        ];

        foreach ($itens as [$nome, $categoria, $descricao, $especificacao]) {
            Equipamento::updateOrCreate(
                ['nome' => $nome, 'categoria' => $categoria],
                [
                    'descricao' => $descricao,
                    'especificacao_resumida' => $especificacao,
                    'unidade_medida' => 'Unidade',
                    'ativo' => true,
                ]
            );
        }
    }
}
