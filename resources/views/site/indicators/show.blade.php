@extends('layouts.site')

@section('title', $indicator->name . ' · Indicadores')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.51.0/dist/apexcharts.min.js"></script>
@endpush

@php
    $accent = $indicator->color ?? $indicator->tenant?->accent_color ?? '#B92828';
    $accentDeep = $indicator->tenant?->accent_deep_color ?? '#8E1B1B';
    $values = $indicator->values->sortBy('recorded_at');
    $goal = (float) ($indicator->goal_value ?? 0);
    $value = (float) ($latest->value ?? 0);
    $progress = $goal > 0 ? min(100, max(0, ($value / $goal) * 100)) : 0;
@endphp

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">
            @if($indicator->tenant){{ $indicator->tenant->short_name }} · @endif
            @if($indicator->category){{ $indicator->category }} · @endif
            Indicador
        </span>
        <h1 class="page-head__title">{{ $indicator->name }}</h1>
        @if($indicator->description)
            <p class="page-head__lead">{{ $indicator->description }}</p>
        @endif
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="indicator-detail">
    <div class="container indicator-detail__inner">
        <div class="indicator-detail__main">

            <div class="indicator-summary" style="--accent:{{ $accent }};--accent-deep:{{ $accentDeep }}">
                <div class="indicator-summary__value">
                    <span class="indicator-summary__big">
                        {{ number_format($value, ($value == intval($value) ? 0 : 2), ',', '.') }}<small>{{ $indicator->unit }}</small>
                    </span>
                    @if($latest?->recorded_at)
                        <span class="indicator-summary__period">{{ $latest->recorded_at->translatedFormat('F \d\e Y') }}</span>
                    @endif
                </div>

                @if($variation !== null)
                    <div class="indicator-summary__variation {{ $variation >= 0 ? 'is-up' : 'is-down' }}">
                        <span class="indicator-summary__arrow">@if($variation >= 0)▲@else▼@endif</span>
                        <strong>{{ number_format(abs($variation), 1, ',', '.') }}%</strong>
                        <small>vs período anterior</small>
                    </div>
                @endif

                @if($goal > 0)
                    <div class="indicator-summary__goal">
                        <span>Meta {{ now()->year }}</span>
                        <strong>{{ number_format($goal, ($goal == intval($goal) ? 0 : 2), ',', '.') }}{{ $indicator->unit }}</strong>
                        <div class="indicator-summary__bar">
                            <div style="width: {{ $progress }}%"></div>
                        </div>
                        <small>{{ number_format($progress, 0, ',', '.') }}% atingido</small>
                    </div>
                @endif
            </div>

            <div id="indicator-chart" class="indicator-chart"
                 data-series='@json($values->values()->map(fn ($v) => ['x' => $v->recorded_at->format('Y-m-d'), 'y' => (float) $v->value])->values())'
                 data-goal="{{ $goal }}"
                 data-color="{{ $accent }}"
                 data-type="{{ $indicator->chart_type }}"
                 data-unit="{{ $indicator->unit }}"
                 data-name="{{ $indicator->name }}">
            </div>

            @if($values->count() > 0)
                <h3 class="indicator-detail__section-title">Histórico</h3>
                <div class="indicator-table-wrapper">
                    <table class="indicator-table">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th>Valor</th>
                                <th>Meta</th>
                                <th>Atingido</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($values->reverse() as $v)
                                @php
                                    $vGoal = (float) ($v->goal_value ?? $indicator->goal_value);
                                    $vProgress = $vGoal > 0 ? min(100, max(0, ($v->value / $vGoal) * 100)) : 0;
                                @endphp
                                <tr>
                                    <td>{{ $v->recorded_at->translatedFormat('M/Y') }}</td>
                                    <td><strong>{{ number_format((float) $v->value, ($v->value == intval($v->value) ? 0 : 2), ',', '.') }}{{ $indicator->unit }}</strong></td>
                                    <td>{{ $vGoal > 0 ? number_format($vGoal, 0, ',', '.') . $indicator->unit : '—' }}</td>
                                    <td>
                                        @if($vGoal > 0)
                                            <span class="indicator-table__progress" style="--p: {{ $vProgress }}%; --accent: {{ $accent }}"></span>
                                            {{ number_format($vProgress, 0, ',', '.') }}%
                                        @else — @endif
                                    </td>
                                    <td>{{ $v->notes ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>

        <aside class="indicator-detail__side">
            <div class="indicator-detail__card" style="--accent: {{ $accent }}">
                <small>Origem do dado</small>
                <strong>{{ \App\Models\Indicator::SOURCES[$indicator->source] ?? 'Manual' }}</strong>
                @if($indicator->last_synced_at)
                    <p>Última sincronização: {{ $indicator->last_synced_at->translatedFormat('d \d\e F, H\hi') }}</p>
                @endif
            </div>
            @if($indicator->tenant)
                <div class="indicator-detail__card">
                    <small>Diretoria</small>
                    <strong>{{ $indicator->tenant->short_name }}</strong>
                    <p>{{ $indicator->tenant->full_name }}</p>
                </div>
            @endif
            @if($related->count())
                <div class="indicator-detail__card">
                    <small>Outros indicadores</small>
                    <ul class="indicator-detail__related">
                        @foreach($related as $r)
                            <li><a href="{{ route('indicadores.show', $r->slug) }}">{{ $r->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>

    </div>
</section>

@push('scripts')
<script>
(function () {
    const el = document.getElementById('indicator-chart');
    if (!el) return;
    const series = JSON.parse(el.dataset.series || '[]');
    const goal = parseFloat(el.dataset.goal) || 0;
    const color = el.dataset.color || '#B92828';
    const type = el.dataset.type || 'line';
    const unit = el.dataset.unit || '';
    const name = el.dataset.name || 'Indicador';

    if (!series.length) return;

    const chartType = ({ line: 'line', area: 'area', bar: 'bar', gauge: 'radialBar', progress: 'bar' })[type] || 'line';

    if (chartType === 'radialBar') {
        const last = series[series.length - 1].y;
        const pct = goal > 0 ? Math.min(100, (last / goal) * 100) : 0;
        new ApexCharts(el, {
            chart: { type: 'radialBar', height: 360 },
            series: [pct],
            colors: [color],
            plotOptions: {
                radialBar: {
                    hollow: { size: '60%' },
                    dataLabels: {
                        name: { fontSize: '14px', color: '#6B7280', offsetY: -10 },
                        value: { fontSize: '36px', fontWeight: 800, color: color, offsetY: 8, formatter: (v) => v.toFixed(0) + '%' },
                    },
                },
            },
            labels: ['Meta atingida'],
            stroke: { lineCap: 'round' },
        }).render();
        return;
    }

    const annotations = goal > 0 ? {
        yaxis: [{
            y: goal,
            borderColor: '#C9A35B',
            strokeDashArray: 6,
            label: {
                text: `Meta: ${goal.toLocaleString('pt-BR')}${unit}`,
                style: { color: '#1F2937', background: '#C9A35B' },
                position: 'right',
                offsetX: 0,
            },
        }],
    } : {};

    new ApexCharts(el, {
        chart: { type: chartType, height: 380, fontFamily: 'Inter, sans-serif', toolbar: { show: false }, animations: { speed: 600 } },
        series: [{ name, data: series }],
        colors: [color],
        stroke: { curve: 'smooth', width: 3 },
        fill: chartType === 'area' ? { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.05 } } : { type: 'solid' },
        markers: { size: 5, strokeWidth: 2, strokeColors: '#fff', hover: { size: 7 } },
        dataLabels: { enabled: false },
        xaxis: {
            type: 'datetime',
            labels: { datetimeFormatter: { year: 'yyyy', month: "MMM 'yy", day: 'dd MMM' }, style: { colors: '#6B7280' } },
            axisBorder: { color: '#E7E2DE' }, axisTicks: { color: '#E7E2DE' },
        },
        yaxis: { labels: { formatter: (v) => v.toLocaleString('pt-BR') + unit, style: { colors: '#6B7280' } } },
        grid: { borderColor: '#E7E2DE', strokeDashArray: 3 },
        annotations,
        tooltip: { theme: 'light', y: { formatter: (v) => v.toLocaleString('pt-BR') + unit } },
    }).render();
})();
</script>
@endpush

@endsection
