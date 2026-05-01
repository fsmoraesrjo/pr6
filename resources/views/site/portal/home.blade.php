@extends('layouts.site')

@section('title', 'PR-6 UERJ · Pró-Reitoria de Planejamento e Gestão')

@php
    $verticals = \App\Models\Tenant::query()
        ->where('is_root', false)->where('is_active', true)
        ->orderBy('order')->get();

    $latestNewsByTenant = $verticals->mapWithKeys(function ($t) {
        $news = \App\Models\News::query()
            ->forTenant($t)
            ->published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
        return [$t->id => $news];
    });

    $allNews = \App\Models\News::query()
        ->acrossTenants()
        ->with('tenant')
        ->published()
        ->orderByDesc('published_at')
        ->limit(5)
        ->get();

    $upcoming = \App\Models\Event::query()
        ->acrossTenants()
        ->with('tenant')
        ->public()
        ->upcoming()
        ->limit(4)
        ->get();

    $featuredIndicators = \App\Models\Indicator::query()
        ->acrossTenants()
        ->with('tenant', 'values')
        ->where('is_public', true)
        ->where('is_featured', true)
        ->orderBy('order')
        ->limit(4)
        ->get();
@endphp

@section('content')

<section class="hero" aria-labelledby="hero-title">
    <div class="hero__bg" aria-hidden="true">
        <div class="hero__gradient"></div>
        <div class="hero__shape hero__shape--1"></div>
        <div class="hero__shape hero__shape--2"></div>
        <div class="hero__grid"></div>
    </div>
    <div class="container hero__inner">
        <div class="hero__content">
            <h1 id="hero-title" class="hero__title">
                Planejar.<br>Gerir.<br>
                <span class="hero__title-accent">Transformar.</span>
            </h1>
            <p class="hero__lead">{{ $tenant->description }}</p>
            <div class="hero__cta">
                <a href="/documentos" class="btn btn--primary">
                    Acesse o PDI 2026-2030
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
                <a href="/sobre" class="btn btn--secondary">Sobre a PR-6</a>
            </div>
        </div>
        <div class="hero__stats" role="list">
            <div class="stat" role="listitem">
                <strong data-count="{{ \App\Models\Document::acrossTenants()->count() ?: 247 }}">0</strong>
                <span>Documentos públicos</span>
            </div>
            <div class="stat" role="listitem">
                <strong data-count="{{ \App\Models\Indicator::acrossTenants()->where('is_public', true)->count() ?: 12 }}">0</strong>
                <span>Indicadores monitorados</span>
            </div>
            <div class="stat" role="listitem">
                <strong data-count="{{ \App\Models\Event::acrossTenants()->whereYear('starts_at', now()->year)->count() ?: 38 }}">0</strong>
                <span>Reuniões em {{ now()->year }}</span>
            </div>
            <div class="stat" role="listitem">
                <strong>100<span>%</span></strong>
                <span>Transparência ativa</span>
            </div>
        </div>
    </div>
</section>


<section class="news-feed" aria-labelledby="feed-title">
    <div class="container">
        <header class="section-head section-head--row">
            <div>
                <span class="section-head__eyebrow">Em destaque</span>
                <h2 id="feed-title" class="section-head__title">No portal hoje</h2>
            </div>
            <div class="news-feed__filter" role="tablist" aria-label="Filtrar por diretoria">
                <button type="button" class="chip is-active" data-filter="all">Todas</button>
                @foreach($verticals as $v)
                    <button type="button" class="chip" data-filter="{{ $v->slug }}" style="--accent:{{ $v->accent_color }}">{{ $v->short_name }}</button>
                @endforeach
            </div>
        </header>

        <div class="news-feed__grid">
            @foreach($allNews as $i => $n)
                @php
                    $accent = $n->tenant?->accent_color ?? '#B92828';
                    $accentDeep = $n->tenant?->accent_deep_color ?? '#8E1B1B';
                @endphp
                <article class="news {{ $i === 0 ? 'news--feature' : '' }}" data-tenant="{{ $n->tenant?->slug }}" style="--accent:{{ $accent }}">
                    <div class="news__cover" style="background-image:linear-gradient(135deg,{{ $accent }},{{ $accentDeep }})">
                        <span class="news__badge">{{ $n->tenant?->short_name }}</span>
                    </div>
                    <div class="news__body">
                        <time class="news__date">{{ $n->published_at?->translatedFormat('d \d\e F, Y') }}</time>
                        <h3 class="news__title">{{ $n->title }}</h3>
                        @if($i === 0)
                            <p class="news__excerpt">{{ $n->summary }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="agenda" aria-labelledby="agenda-title">
    <div class="container agenda__inner">
        <div class="agenda__copy">
            <span class="section-head__eyebrow">Agenda institucional</span>
            <h2 id="agenda-title" class="section-head__title">Próximas reuniões e eventos</h2>
            <p>Tudo o que está acontecendo na PR-6 e nas quatro diretorias, em um único calendário público. Filtre, exporte para o seu calendário e acompanhe ao vivo as reuniões transmitidas.</p>
            <a href="/agenda" class="btn btn--primary">Ver agenda completa</a>
        </div>
        <ul class="agenda__list">
            @foreach($upcoming as $e)
                @php $accent = $e->tenant?->accent_color ?? '#B92828'; @endphp
                <li class="agenda-item" style="--accent:{{ $accent }}">
                    <div class="agenda-item__date">
                        <strong>{{ $e->starts_at->format('d') }}</strong>
                        <span>{{ \Illuminate\Support\Str::upper($e->starts_at->translatedFormat('M')) }}</span>
                    </div>
                    <div class="agenda-item__body">
                        <span class="agenda-item__type">{{ ucfirst($e->type) }} · {{ $e->tenant?->short_name }}</span>
                        <h3>{{ $e->title }}</h3>
                        <p>{{ $e->starts_at->format('H\hi') }}@if($e->ends_at) às {{ $e->ends_at->format('H\hi') }}@endif · {{ $e->location }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</section>

<section class="indicators" aria-labelledby="indicators-title">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow">Transparência</span>
            <h2 id="indicators-title" class="section-head__title">Indicadores em tempo real</h2>
            <p class="section-head__lead">Acompanhe a execução das metas do PDI, do orçamento e dos compromissos institucionais da PR-6.</p>
        </header>

        <div class="indicators__grid">
            @foreach($featuredIndicators as $ind)
                @php
                    $latest = $ind->values->sortByDesc('recorded_at')->first();
                    $value = $latest?->value ?? 0;
                    $goal = $ind->goal_value ?? 100;
                    $progress = $goal > 0 ? min(100, ($value / $goal) * 100) : 0;
                @endphp
                <article class="indicator">
                    <header>
                        <span>{{ $ind->name }}</span>
                        <strong>{{ number_format((float) $value, 0, ',', '.') }}<span>{{ $ind->unit }}</span></strong>
                    </header>
                    <div class="indicator__bar">
                        <div style="width:{{ $progress }}%"></div>
                    </div>
                    <footer>{{ $ind->tenant?->short_name }} · Meta {{ now()->year }}: {{ number_format((float) $goal, 0, ',', '.') }}{{ $ind->unit }}</footer>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="ouvidoria" aria-labelledby="contato-title">
    <div class="container ouvidoria__inner">
        <div class="ouvidoria__copy">
            <span class="section-head__eyebrow">Tire suas dúvidas</span>
            <h2 id="contato-title" class="section-head__title">Fale conosco</h2>
            <p>Dúvidas sobre os trabalhos da PR-6, solicitações de informação institucional ou interesse em parcerias. Respondemos rapidamente.</p>
            <ul class="ouvidoria__list">
                <li>Resposta em até 5 dias úteis</li>
                <li>Encaminhamento direto à área responsável</li>
                <li>Para reclamações e elogios, utilize a Ouvidoria UERJ</li>
            </ul>
        </div>
        <a href="/fale-conosco" class="ouvidoria__cta">
            Enviar mensagem
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>

@endsection
