@extends('layouts.site')

@section('title', 'Pessoas · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Equipe e contatos</span>
        <h1 class="page-head__title">Pessoas{{ $tenant->is_root ? '' : ' da ' . $tenant->short_name }}</h1>
        <p class="page-head__lead">
            {{ $totalActive }} {{ $totalActive === 1 ? 'profissional' : 'profissionais' }} na
            {{ $tenant->is_root ? 'PR-6' : $tenant->short_name }}.
            Use a busca ou os filtros para encontrar quem precisa.
        </p>
        <form action="{{ route('pessoas') }}" method="get" class="page-head__search">
            <input type="search" name="q" value="{{ $q }}" placeholder="Buscar por nome, cargo ou e-mail..." aria-label="Buscar pessoa">
            @if($diretoria)<input type="hidden" name="d" value="{{ $diretoria }}">@endif
            @if($unidade)<input type="hidden" name="u" value="{{ $unidade }}">@endif
            <button type="submit" aria-label="Buscar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            </button>
        </form>
        <div class="page-head__cta">
            <a href="/organograma" class="btn btn--gold">
                Ver organograma
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="people-page-v2">
    <div class="container">

        <div class="people-filters">
            @if($isPortal && $tenants->count() > 1)
                <div class="people-filter-group">
                    <span class="people-filter-group__label">Diretoria</span>
                    <div class="people-chips">
                        <a href="{{ route('pessoas') }}{{ $q ? '?q=' . urlencode($q) : '' }}"
                           class="people-chip {{ ! $diretoria ? 'is-active' : '' }}">Todas</a>
                        @foreach($tenants as $t)
                            <a href="{{ route('pessoas') }}?d={{ $t->slug }}{{ $q ? '&q=' . urlencode($q) : '' }}"
                               class="people-chip {{ $diretoria === $t->slug ? 'is-active' : '' }}"
                               style="--chip-accent:{{ $t->accent_color }}">{{ $t->short_name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($units->count() > 0)
                <div class="people-filter-group">
                    <span class="people-filter-group__label">Unidade</span>
                    <select onchange="if(this.value){window.location=this.value}else{window.location='{{ route('pessoas') }}{{ $diretoria ? '?d=' . $diretoria : '' }}{{ $q ? ($diretoria ? '&' : '?') . 'q=' . urlencode($q) : '' }}'}">
                        <option value="">Todas as unidades</option>
                        @foreach($units as $u)
                            @php
                                $params = http_build_query(array_filter([
                                    'd' => $diretoria,
                                    'q' => $q ?: null,
                                    'u' => $u->slug,
                                ]));
                            @endphp
                            <option value="{{ route('pessoas') }}?{{ $params }}" {{ $unidade === $u->slug ? 'selected' : '' }}>
                                @if($isPortal && $u->tenant){{ $u->tenant->short_name }} · @endif{{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($q || $diretoria || $unidade)
                <a href="{{ route('pessoas') }}" class="people-clear">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    Limpar filtros
                </a>
            @endif
        </div>

        <p class="people-result-count">
            {{ $members->total() }} {{ $members->total() === 1 ? 'resultado' : 'resultados' }}
            @if($q) para "<strong>{{ $q }}</strong>"@endif
        </p>

        @if($members->count() === 0)
            <div class="empty-state" style="margin-top: 2rem;">
                Nenhum profissional encontrado com os filtros atuais.
                <br><br>
                <a href="{{ route('pessoas') }}" class="btn btn--primary">Limpar filtros</a>
            </div>
        @else
            <div class="people-grid-v2">
                @foreach($members as $m)
                    @php $accent = $m->tenant?->accent_color ?? '#B92828'; @endphp
                    <article class="person-row {{ $m->is_head ? 'person-row--head' : '' }}" style="--accent:{{ $accent }}">
                        <div class="person-row__photo">
                            @if($m->photo_path)
                                <img src="{{ asset('storage/' . $m->photo_path) }}" alt="{{ $m->name }}" loading="lazy" decoding="async" width="56" height="56">
                            @else
                                <span aria-hidden="true">{{ collect(explode(' ', $m->name))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}</span>
                            @endif
                            @if($m->is_head)<span class="person-row__crown" title="Titular" aria-label="Titular">★</span>@endif
                        </div>
                        <div class="person-row__body">
                            <h3>{{ $m->name }}</h3>
                            <p class="person-row__role">{{ $m->role_title }}</p>
                            <p class="person-row__meta">
                                @if($isPortal && $m->tenant)
                                    <span class="person-row__tenant">{{ $m->tenant->short_name }}</span>
                                @endif
                                @if($m->orgUnit)
                                    <span>· {{ $m->orgUnit->name }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="person-row__contact">
                            @if($m->email)
                                <a href="mailto:{{ $m->email }}" title="{{ $m->email }}" aria-label="E-mail de {{ $m->name }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
                                </a>
                            @endif
                            @if($m->phone)
                                <a href="tel:{{ preg_replace('/\D/', '', $m->phone) }}" title="{{ $m->phone }}" aria-label="Telefone de {{ $m->name }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="pagination-wrapper">{{ $members->links() }}</div>
        @endif

    </div>
</section>
@endsection
