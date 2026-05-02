@extends('layouts.site')

@section('title', 'Sobre · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Institucional</span>
        <h1 class="page-head__title">{{ $tenant->is_root ? 'Sobre a PR-6' : 'Sobre a ' . $tenant->short_name }}</h1>
        <p class="page-head__lead">{{ $tenant->description ?: 'Conheça a missão, a estrutura e os profissionais que tocam o dia a dia da ' . $tenant->short_name . '.' }}</p>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="about-page">
    <div class="container">

        <div class="about-pillars">
            <article class="about-pillar">
                <div class="about-pillar__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <h3>Missão</h3>
                <p>{{ $tenant->is_root
                    ? 'Articular tecnologia, infraestrutura, planejamento e sustentabilidade para sustentar a missão de ensino, pesquisa e extensão da UERJ.'
                    : 'Sustentar as ações da ' . $tenant->short_name . ' alinhadas à missão institucional da Pró-Reitoria de Planejamento e Gestão.' }}</p>
            </article>
            <article class="about-pillar">
                <div class="about-pillar__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s4-8 10-8 10 8 10 8-4 8-10 8S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <h3>Visão</h3>
                <p>Ser referência em planejamento e gestão na universidade pública brasileira, com transparência, governança e foco no impacto social.</p>
            </article>
            <article class="about-pillar">
                <div class="about-pillar__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 6 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1 3-6z"/></svg>
                </div>
                <h3>Valores</h3>
                <p>Transparência, ética, eficiência, sustentabilidade, inovação e respeito à comunidade universitária.</p>
            </article>
        </div>

        @if($tenant->is_root)
        <div class="about-text">
            <h2>O que fazemos</h2>
            <p>A Pró-Reitoria de Planejamento e Gestão (PR-6) é a área da UERJ responsável pela articulação estratégica entre orçamento, infraestrutura, tecnologia e sustentabilidade. Atuamos em quatro grandes frentes, organizadas em diretorias específicas, cada uma com autonomia operacional e identidade própria.</p>
            <p>Nossa atuação abrange desde a modernização da rede acadêmica até a gestão dos espaços físicos, passando pelo planejamento de obras, pelo controle de contratos administrativos e pela implementação de práticas ambientais que reduzem o impacto da operação universitária.</p>
        </div>
        @endif

        @if($verticals->count())
        <div class="about-verticals">
            <h2>Diretorias</h2>
            <div class="about-verticals__grid">
                @foreach($verticals as $v)
                    <a href="{{ $v->url() }}" class="about-vertical-card"
                       style="--accent:{{ $v->accent_color }};--accent-soft:{{ $v->accent_soft_color }};--accent-deep:{{ $v->accent_deep_color }}">
                        <span class="about-vertical-card__icon" aria-hidden="true">
                            <x-site.tenant-icon :slug="$v->slug" />
                        </span>
                        <h3>{{ $v->short_name }}</h3>
                        <p>{{ $v->full_name }}</p>
                        <small>{{ $v->tagline }}</small>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($head)
        <div class="about-leader">
            <div class="about-leader__photo">
                @if($head->photo_path)
                    <img src="{{ asset('storage/' . $head->photo_path) }}" alt="{{ $head->name }}">
                @else
                    <span aria-hidden="true">{{ collect(explode(' ', $head->name))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}</span>
                @endif
            </div>
            <div class="about-leader__body">
                <span class="about-leader__role">Liderança</span>
                <h3>{{ $head->name }}</h3>
                <p class="about-leader__title">{{ $head->role_title }}</p>
                @if($head->bio)<p class="about-leader__bio">{{ $head->bio }}</p>@endif
                <div class="about-leader__cta">
                    <a href="/pessoas" class="btn btn--ghost">Conheça a equipe completa</a>
                    <a href="/organograma" class="btn btn--ghost">Ver organograma</a>
                </div>
            </div>
        </div>
        @endif

        <div class="about-cta">
            <h2>Transparência ativa</h2>
            <p>Acompanhe documentos, agenda institucional, indicadores em tempo real e canais de contato.</p>
            <div class="about-cta__buttons">
                <a href="/documentos" class="btn btn--primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                    Documentos
                </a>
                <a href="/indicadores" class="btn btn--secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 5-6"/></svg>
                    Indicadores
                </a>
                <a href="/agenda" class="btn btn--secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    Agenda
                </a>
                <a href="/contato" class="btn btn--secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Fale conosco
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
