@extends('layouts.site')

@section('title', 'Painel de indicadores · ' . $tenant->short_name)

@push('head')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.51.0/dist/apexcharts.min.js"></script>
@endpush

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Transparência ativa</span>
        <h1 class="page-head__title">Painel de indicadores</h1>
        <p class="page-head__lead">Acompanhe a execução das metas do PDI, do orçamento, dos compromissos institucionais e dos serviços oferecidos pela {{ $tenant->is_root ? 'PR-6' : $tenant->short_name }}.</p>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="indicators-page">
    <div class="container">

        @if($indicators->isEmpty())
            <p class="empty-state">Nenhum indicador público publicado ainda. Em breve, todos os dados de gestão estarão disponíveis aqui.</p>
        @else
            <div class="indicators-grid">
                @foreach($indicators as $i => $ind)
                    @php
                        $accent = $ind->color ?? $ind->tenant?->accent_color ?? '#B92828';
                        $latest = $ind->values->last();
                        $value = $latest?->value ?? 0;
                        $goal = $ind->goal_value ?? 100;
                        $progress = $goal > 0 ? min(100, max(0, ($value / $goal) * 100)) : 0;
                        $series = $ind->values->take(-12)->map(fn ($v) => (float) $v->value)->values();
                        $previous = $ind->values->slice(-2, 1)->first();
                        $variation = ($previous && $previous->value > 0) ? (($value - $previous->value) / $previous->value) * 100 : null;
                    @endphp
                    <a href="{{ route('indicadores.show', $ind->slug) }}"
                       class="indicator-card"
                       style="--accent:{{ $accent }}">
                        <header>
                            @if($ind->tenant && $tenant->is_root)
                                <span class="indicator-card__tenant">{{ $ind->tenant->short_name }}</span>
                            @endif
                            @if($ind->category)<span class="indicator-card__category">{{ $ind->category }}</span>@endif
                            @if($variation !== null)
                                <span class="indicator-card__variation {{ $variation >= 0 ? 'is-up' : 'is-down' }}">
                                    @if($variation >= 0)▲@else▼@endif
                                    {{ number_format(abs($variation), 1, ',', '.') }}%
                                </span>
                            @endif
                        </header>
                        <h2>{{ $ind->name }}</h2>
                        <div class="indicator-card__value">
                            <strong>{{ number_format((float) $value, ($value == intval($value) ? 0 : 1), ',', '.') }}</strong>
                            <span>{{ $ind->unit }}</span>
                        </div>
                        @if($ind->chart_type === 'progress')
                            <div class="indicator-card__bar">
                                <div style="width: {{ $progress }}%"></div>
                            </div>
                            <small>Meta: {{ number_format((float) $goal, ($goal == intval($goal) ? 0 : 1), ',', '.') }}{{ $ind->unit }}</small>
                        @else
                            <div class="indicator-card__spark" data-spark='@json($series)' data-color="{{ $accent }}"></div>
                            <small>Meta: {{ number_format((float) $goal, ($goal == intval($goal) ? 0 : 1), ',', '.') }}{{ $ind->unit }} · Últimos {{ $series->count() }} períodos</small>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif

    </div>
</section>

@push('scripts')
<script>
document.querySelectorAll('.indicator-card__spark').forEach(el => {
    const data = JSON.parse(el.dataset.spark || '[]');
    const color = el.dataset.color || '#B92828';
    if (!data.length) return;
    new ApexCharts(el, {
        chart: { type: 'area', height: 60, sparkline: { enabled: true }, animations: { enabled: false } },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
        series: [{ data }],
        colors: [color],
        tooltip: { enabled: false },
    }).render();
});
</script>
@endpush

@endsection
