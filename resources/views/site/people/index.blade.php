@extends('layouts.site')

@section('title', 'Pessoas · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Equipe e contatos</span>
        <h1 class="page-head__title">Pessoas{{ $tenant->is_root ? '' : ' da ' . $tenant->short_name }}</h1>
        <p class="page-head__lead">Conheça os profissionais que tocam o dia a dia da {{ $tenant->is_root ? 'PR-6' : $tenant->short_name }}, organizados por unidade.</p>
        <div class="page-head__cta">
            <a href="/organograma" class="btn btn--gold">
                Ver organograma completo
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="people-page">
    <div class="container">

        @forelse($byUnit as $unitId => $members)
            @php
                $unit = $unitsById->get($unitId);
                $accent = $unit?->tenant?->accent_color ?? '#B92828';
            @endphp
            <div class="people-unit" style="--accent:{{ $accent }}">
                <header class="people-unit__head">
                    <h2>{{ $unit?->name ?? 'Equipe' }}</h2>
                    @if($unit?->tenant && $isPortal)
                        <span class="people-unit__badge">{{ $unit->tenant->short_name }}</span>
                    @endif
                    @if($unit?->description)
                        <p>{{ $unit->description }}</p>
                    @endif
                </header>

                <div class="people-grid">
                    @foreach($members as $m)
                        <article class="person-card {{ $m->is_head ? 'person-card--head' : '' }}">
                            <div class="person-card__photo">
                                @if($m->photo_path)
                                    <img src="{{ asset('storage/' . $m->photo_path) }}" alt="{{ $m->name }}" loading="lazy">
                                @else
                                    <span class="person-card__initials" aria-hidden="true">
                                        {{ collect(explode(' ', $m->name))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
                                    </span>
                                @endif
                                @if($m->is_head)
                                    <span class="person-card__badge" title="Titular da unidade">Titular</span>
                                @endif
                            </div>
                            <div class="person-card__body">
                                <h3>{{ $m->name }}</h3>
                                <p>{{ $m->role_title }}</p>
                                @if($m->email || $m->phone)
                                    <div class="person-card__contact">
                                        @if($m->email)
                                            <a href="mailto:{{ $m->email }}" aria-label="E-mail">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16v12H4zM4 6l8 6 8-6"/></svg>
                                                {{ $m->email }}
                                            </a>
                                        @endif
                                        @if($m->phone)
                                            <span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 4h4l2 5-3 2a12 12 0 0 0 6 6l2-3 5 2v4a2 2 0 0 1-2 2A18 18 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg>
                                                {{ $m->phone }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                @if($m->bio)
                                    <p class="person-card__bio">{{ $m->bio }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="empty-state">Equipe em cadastramento. Em breve, todos os profissionais da {{ $tenant->short_name }} estarão listados aqui.</p>
        @endforelse

    </div>
</section>
@endsection
