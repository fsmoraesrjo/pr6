@extends('layouts.site')

@section('title', $tenant->short_name . ' · ' . $tenant->full_name)

@php
    $news = \App\Models\News::query()->published()->orderByDesc('published_at')->limit(6)->get();
    $upcoming = \App\Models\Event::query()->public()->upcoming()->limit(4)->get();
    $indicators = \App\Models\Indicator::query()->where('is_public', true)->where('is_featured', true)->with('values')->limit(3)->get();
@endphp

@section('content')

<section class="page-head page-head--hero" aria-labelledby="hero-title">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->full_name }}</span>
        <h1 id="hero-title" class="page-head__title">{{ $tenant->tagline }}</h1>
        <p class="page-head__lead">{{ $tenant->description }}</p>
        <div class="page-head__cta">
            <a href="/servicos" class="btn btn--gold">
                Catálogo de serviços
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
            </a>
            <a href="/sobre" class="btn btn--white-outline">Sobre a {{ $tenant->short_name }}</a>
        </div>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="news-feed" aria-labelledby="feed-title">
    <div class="container">
        <header class="section-head section-head--row">
            <div>
                <span class="section-head__eyebrow" style="color: {{ $tenant->accent_color }}">Últimas notícias</span>
                <h2 id="feed-title" class="section-head__title">Em pauta na {{ $tenant->short_name }}</h2>
            </div>
            <a href="/noticias" class="btn btn--ghost">Todas as notícias</a>
        </header>

        <div class="news-feed__grid">
            @foreach($news as $i => $n)
                <article class="news {{ $i === 0 ? 'news--feature' : '' }}" style="--accent:{{ $tenant->accent_color }}">
                    <div class="news__cover" style="background-image:linear-gradient(135deg,{{ $tenant->accent_color }},{{ $tenant->accent_deep_color }})">
                        <span class="news__badge">{{ $tenant->short_name }}</span>
                    </div>
                    <div class="news__body">
                        <time class="news__date">{{ $n->published_at?->translatedFormat('d \d\e F, Y') }}</time>
                        <h3 class="news__title">{{ $n->title }}</h3>
                        @if($i === 0)<p class="news__excerpt">{{ $n->summary }}</p>@endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="agenda" aria-labelledby="agenda-title">
    <div class="container agenda__inner">
        <div class="agenda__copy">
            <span class="section-head__eyebrow" style="color: {{ $tenant->accent_color }}">Agenda</span>
            <h2 id="agenda-title" class="section-head__title">Próximos compromissos</h2>
            <p>Reuniões, prazos e eventos da {{ $tenant->short_name }} consolidados em um só lugar.</p>
            <a href="/agenda" class="btn btn--primary" style="background:linear-gradient(135deg, var(--accent), var(--accent-deep))">Ver agenda completa</a>
        </div>
        <ul class="agenda__list">
            @foreach($upcoming as $e)
                <li class="agenda-item" style="--accent:{{ $tenant->accent_color }}">
                    <div class="agenda-item__date">
                        <strong>{{ $e->starts_at->format('d') }}</strong>
                        <span>{{ \Illuminate\Support\Str::upper($e->starts_at->translatedFormat('M')) }}</span>
                    </div>
                    <div class="agenda-item__body">
                        <span class="agenda-item__type">{{ ucfirst($e->type) }}</span>
                        <h3>{{ $e->title }}</h3>
                        <p>{{ $e->starts_at->format('H\hi') }} · {{ $e->location }}</p>
                    </div>
                </li>
            @endforeach
            @if($upcoming->isEmpty())
                <li class="agenda-item" style="--accent:{{ $tenant->accent_color }}">
                    <div class="agenda-item__body">
                        <h3>Nenhum compromisso público registrado</h3>
                        <p>Volte em breve para conferir a agenda da {{ $tenant->short_name }}.</p>
                    </div>
                </li>
            @endif
        </ul>
    </div>
</section>

@if($indicators->isNotEmpty())
<section class="indicators" aria-labelledby="indicators-title">
    <div class="container">
        <header class="section-head">
            <span class="section-head__eyebrow" style="color: {{ $tenant->accent_color }}">Transparência</span>
            <h2 id="indicators-title" class="section-head__title">Indicadores da {{ $tenant->short_name }}</h2>
        </header>
        <div class="indicators__grid">
            @foreach($indicators as $ind)
                @php
                    $latest = $ind->values->sortByDesc('recorded_at')->first();
                    $value = $latest?->value ?? 0;
                    $goal = $ind->goal_value ?? 100;
                    $progress = $goal > 0 ? min(100, ($value / $goal) * 100) : 0;
                @endphp
                <article class="indicator">
                    <header>
                        <span>{{ $ind->name }}</span>
                        <strong style="color: {{ $tenant->accent_color }}">{{ number_format((float) $value, 0, ',', '.') }}<span>{{ $ind->unit }}</span></strong>
                    </header>
                    <div class="indicator__bar">
                        <div style="width:{{ $progress }}%;background:linear-gradient(90deg, {{ $tenant->accent_color }}, {{ $tenant->accent_deep_color }})"></div>
                    </div>
                    <footer>Meta {{ now()->year }}: {{ number_format((float) $goal, 0, ',', '.') }}{{ $ind->unit }}</footer>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
