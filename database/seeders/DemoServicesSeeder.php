<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoServicesSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all()->keyBy('slug');

        $services = [
            // DIRTEC
            ['dirtec', 'Abertura de chamado de TI', 'internal_form', 'Solicite suporte a sistemas, redes ou equipamentos.',
                'Servidores, terceirizados e bolsistas com cadastro institucional.',
                ['Tenha em mãos seu número de matrícula UERJ', 'Descreva o equipamento ou sistema afetado', 'Informe o local físico se for problema de rede']],
            ['dirtec', 'Solicitação de e-mail institucional', 'email', 'Criação ou alteração de conta @uerj.br.',
                'Servidores e docentes recém-admitidos.',
                ['Cópia do contrato ou portaria de nomeação', 'Aprovação da chefia imediata']],
            ['dirtec', 'Acesso à rede sem fio (Wi-Fi UERJ)', 'info_only', 'Conecte-se à rede sem fio dos campi.',
                'Toda comunidade UERJ.',
                ['Use suas credenciais do SISUERJ na rede UERJ', 'Convidados precisam de cadastro temporário pela DIRTEC']],
            // DIRGIS
            ['dirgis', 'Solicitação de manutenção predial', 'internal_form', 'Reparos elétricos, hidráulicos, civis e de mobiliário.',
                'Servidores responsáveis por unidades.',
                ['Localização exata (bloco, andar, sala)', 'Foto do problema (se possível)']],
            ['dirgis', 'Reserva de espaço físico', 'internal_form', 'Reserve auditórios, salas de reunião e áreas comuns.',
                'Servidores e docentes UERJ.',
                ['Data, horário e duração', 'Quantidade de participantes', 'Equipamentos necessários']],
            // DIRPLAG
            ['dirplag', 'Solicitação de projeto de obras', 'email', 'Avaliação técnica para reformas e ampliações.',
                'Diretores de unidade e chefias.',
                ['Justificativa técnica e pedagógica', 'Estimativa de área impactada', 'Orçamento previsto']],
            // COOMAS
            ['coomas', 'Adesão ao programa de coleta seletiva', 'internal_form', 'Inclua sua unidade no programa de gestão de resíduos.',
                'Coordenadores de unidades acadêmicas e administrativas.',
                ['Pessoa de contato responsável', 'Localização dos pontos de coleta sugeridos']],
            ['coomas', 'Diagnóstico de eficiência energética', 'email', 'Avaliação de consumo e recomendações.',
                'Unidades acadêmicas e administrativas.',
                ['Cópias das últimas 6 contas de energia', 'Croqui da unidade (se possível)']],
        ];

        foreach ($services as $i => [$slug, $title, $reqType, $summary, $audience, $reqs]) {
            $tenant = $tenants[$slug] ?? null;
            if (! $tenant) continue;

            Service::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'summary' => $summary,
                    'description' => '<p>' . $summary . '</p><p>Este serviço faz parte do compromisso da ' . $tenant->short_name . ' com a transparência e a eficiência operacional na UERJ.</p>',
                    'audience' => $audience,
                    'request_type' => $reqType,
                    'request_email' => $reqType === 'email' ? str_replace('-', '', $tenant->slug) . '@uerj.br' : null,
                    'requirements' => $reqs,
                    'is_active' => true,
                    'is_featured' => $i < 4,
                    'order' => $i,
                ]
            );
        }
    }
}
