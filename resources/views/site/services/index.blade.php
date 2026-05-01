@extends('layouts.site')

@section('title', 'Serviços · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Catálogo de serviços</span>
        <h1 class="page-head__title">Serviços da {{ $tenant->short_name }}</h1>
        <p class="page-head__lead">Conheça o que a {{ $tenant->full_name }} oferece à comunidade UERJ.</p>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="services-grid">
    <div class="container">
        @if($services->isEmpty())
            <p class="empty-state">Catálogo em construção. Em breve, todos os serviços da {{ $tenant->short_name }} estarão disponíveis aqui.</p>
        @else
            <div class="services-grid__inner">
                @foreach($services as $s)
                    <a href="/servicos/{{ $s->slug }}" class="service-card" style="--accent:{{ $tenant->accent_color }};--accent-soft:{{ $tenant->accent_soft_color }};--accent-deep:{{ $tenant->accent_deep_color }}">
                        <div class="service-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h6M12 9v6"/><circle cx="12" cy="12" r="9"/></svg>
                        </div>
                        <h3>{{ $s->title }}</h3>
                        @if($s->summary)<p>{{ $s->summary }}</p>@endif
                        @if($s->audience)<small>Para: {{ $s->audience }}</small>@endif
                        <span class="service-card__cta">
                            Saiba mais
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
