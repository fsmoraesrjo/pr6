<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Indicator;
use App\Models\IndicatorValue;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all()->keyBy('slug');

        $news = [
            ['pr6', 'Pró-Reitoria divulga calendário do PDI 2026-2030', 'Documento entra em consulta pública na próxima semana com etapas de coleta de contribuições da comunidade.', '2026-04-30'],
            ['pr6', 'Comissão Estratégica aprova revisão orçamentária', 'Mudanças refletem ajustes de prioridade nos investimentos em infraestrutura e tecnologia.', '2026-04-22'],
            ['dirtec', 'Nova rede sem fio chega ao campus Maracanã com cobertura total', 'Projeto de modernização cobre todos os blocos com Wi-Fi 6 e autenticação via SSO.', '2026-05-02'],
            ['dirtec', 'Portal de chamados ganha integração com SISUERJ', 'Servidores agora abrem chamados sem precisar de credenciais separadas.', '2026-04-28'],
            ['dirtec', 'DIRTEC abre seleção para bolsistas de TI', 'Vagas para alunos de graduação atuarem em projetos de modernização.', '2026-04-20'],
            ['dirgis', 'Reforma do bloco F entra na fase final', 'Obras de revitalização incluem acessibilidade total e novos sanitários.', '2026-04-30'],
            ['dirgis', 'Novos contratos de limpeza são publicados', 'Modelo prevê controle de qualidade por auditoria mensal.', '2026-04-25'],
            ['dirgis', 'Programa de eficiência energética avança', 'Substituição de luminárias para LED reduz consumo em 28%.', '2026-04-18'],
            ['dirplag', 'Plano Diretor 2026-2030 entra em consulta pública', 'Documento com diretrizes de expansão fica disponível por 60 dias.', '2026-05-01'],
            ['dirplag', 'Novo laboratório do CTC tem projeto aprovado', 'Espaço será compartilhado entre engenharia e ciências da computação.', '2026-04-22'],
            ['dirplag', 'Estudo de retrofit da biblioteca central', 'Diagnóstico técnico aponta intervenções necessárias para os próximos cinco anos.', '2026-04-15'],
            ['coomas', 'UERJ adere a programa de neutralização de carbono', 'Pacto firmado prevê metas anuais de redução de emissões.', '2026-04-29'],
            ['coomas', 'Coleta seletiva ganha novos pontos', 'Campanha amplia abrangência para todos os campi.', '2026-04-21'],
            ['coomas', 'Semana do Meio Ambiente UERJ 2026 tem programação divulgada', 'Cinco dias de palestras, oficinas e atividades comunitárias.', '2026-04-10'],
        ];

        foreach ($news as [$slug, $title, $summary, $publishedAt]) {
            $tenant = $tenants[$slug] ?? null;
            if (! $tenant) continue;

            News::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'summary' => $summary,
                    'body' => '<p>' . $summary . '</p><p>Conteúdo de demonstração para o ambiente de desenvolvimento da PR-6.</p>',
                    'published_at' => Carbon::parse($publishedAt),
                    'is_featured' => false,
                ]
            );
        }

        $events = [
            ['pr6', 'Conselho Estratégico de Planejamento', 'reuniao', '2026-05-05 14:00', '2026-05-05 17:00', 'Sala dos Conselhos'],
            ['dirtec', 'Workshop: Modernização da Rede Sem Fio', 'workshop', '2026-05-08 10:00', '2026-05-08 12:00', 'Auditório do CTC'],
            ['coomas', 'Semana do Meio Ambiente UERJ 2026', 'evento', '2026-05-12 09:00', '2026-05-16 18:00', 'Diversos campi'],
            ['dirplag', 'Audiência Pública do Plano Diretor 2026-2030', 'consulta', '2026-05-20 19:00', '2026-05-20 21:30', 'Salão Nobre'],
            ['dirgis', 'Vistoria Técnica da Reforma do Bloco F', 'reuniao', '2026-05-09 10:00', '2026-05-09 12:00', 'Bloco F, sala 101'],
        ];

        foreach ($events as [$slug, $title, $type, $start, $end, $location]) {
            $tenant = $tenants[$slug] ?? null;
            if (! $tenant) continue;

            Event::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'description' => 'Evento institucional registrado no portal da PR-6.',
                    'type' => $type,
                    'starts_at' => Carbon::parse($start),
                    'ends_at' => Carbon::parse($end),
                    'location' => $location,
                    'is_public' => true,
                ]
            );
        }

        $indicators = [
            ['pr6', 'Execução orçamentária', 'progress', '%', 95, [
                ['2026-01', 18], ['2026-02', 32], ['2026-03', 51], ['2026-04', 78],
            ]],
            ['pr6', 'Metas do PDI cumpridas', 'progress', '%', 100, [
                ['2026-01', 12], ['2026-02', 28], ['2026-03', 47], ['2026-04', 64],
            ]],
            ['dirtec', 'Chamados de TI resolvidos', 'progress', '%', 95, [
                ['2026-01', 88], ['2026-02', 90], ['2026-03', 91], ['2026-04', 92],
            ]],
            ['coomas', 'Resíduos reciclados', 'progress', '%', 60, [
                ['2026-01', 28], ['2026-02', 33], ['2026-03', 38], ['2026-04', 41],
            ]],
        ];

        foreach ($indicators as [$slug, $name, $chart, $unit, $goal, $values]) {
            $tenant = $tenants[$slug] ?? null;
            if (! $tenant) continue;

            $indicator = Indicator::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'chart_type' => $chart,
                    'unit' => $unit,
                    'goal_value' => $goal,
                    'is_public' => true,
                    'is_featured' => true,
                ]
            );

            foreach ($values as [$period, $value]) {
                IndicatorValue::updateOrCreate(
                    ['indicator_id' => $indicator->id, 'period' => $period],
                    [
                        'value' => $value,
                        'goal_value' => $goal,
                        'recorded_at' => Carbon::parse($period . '-15'),
                    ]
                );
            }
        }
    }
}
