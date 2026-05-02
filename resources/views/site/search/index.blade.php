@extends('layouts.site')

@section('title', $q ? 'Busca: ' . $q : 'Buscar no portal')

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Busca no portal</span>
        <h1 class="page-head__title">{{ $q ? 'Resultados para "' . $q . '"' : 'Buscar no portal' }}</h1>
        <p class="page-head__lead">
            @if($q && $totalAll > 0)
                {{ $totalAll }} {{ $totalAll === 1 ? 'resultado encontrado' : 'resultados encontrados' }} entre notícias, documentos, serviços e pessoas.
            @elseif($q)
                Nenhum resultado encontrado. Tente outros termos.
            @else
                Encontre notícias, documentos, serviços e profissionais da {{ $tenant->is_root ? 'PR-6' : $tenant->short_name }}.
            @endif
        </p>
        <form action="{{ route('search') }}" method="get" class="page-head__search">
            <input type="search" name="q" value="{{ $q }}" placeholder="Digite o que procura..." aria-label="Buscar" autofocus>
            <button type="submit" aria-label="Buscar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            </button>
        </form>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="search-page">
    <div class="container">

        @if($q && $totalAll > 0)
            <div class="search-tabs" role="tablist">
                <a href="?q={{ urlencode($q) }}&tab=all" class="search-tab {{ $tab === 'all' ? 'is-active' : '' }}">
                    Tudo <span>{{ $totalAll }}</span>
                </a>
                @foreach(['news' => 'Notícias', 'documents' => 'Documentos', 'services' => 'Serviços', 'people' => 'Pessoas'] as $key => $label)
                    @if($totals[$key] > 0)
                        <a href="?q={{ urlencode($q) }}&tab={{ $key }}" class="search-tab {{ $tab === $key ? 'is-active' : '' }}">
                            {{ $label }} <span>{{ $totals[$key] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>

            @if($tab === 'all' || $tab === 'news')
                @if($results['news']->count() > 0)
                    <h2 class="search-group-title">Notícias</h2>
                    <div class="search-results">
                        @foreach($results['news'] as $n)
                            @php $accent = $n->tenant?->accent_color ?? '#B92828'; @endphp
                            <a href="/noticias/{{ $n->slug }}" class="search-result" style="--accent:{{ $accent }}">
                                <div class="search-result__icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8z"/></svg>
                                </div>
                                <div class="search-result__body">
                                    <span class="search-result__type">Notícia · {{ $n->tenant?->short_name }}</span>
                                    <h3>{!! \App\Support\SearchHighlight::apply($n->title, $q) !!}</h3>
                                    @if($n->summary)
                                        <p>{!! \App\Support\SearchHighlight::apply(\Illuminate\Support\Str::limit($n->summary, 180), $q) !!}</p>
                                    @endif
                                    <small>{{ $n->published_at?->translatedFormat('d \d\e F \d\e Y') }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endif

            @if($tab === 'all' || $tab === 'documents')
                @if($results['documents']->count() > 0)
                    <h2 class="search-group-title">Documentos</h2>
                    <div class="search-results">
                        @foreach($results['documents'] as $d)
                            @php $accent = $d->tenant?->accent_color ?? '#B92828'; @endphp
                            <a href="/documentos/{{ $d->slug }}" class="search-result" style="--accent:{{ $accent }}">
                                <div class="search-result__icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                </div>
                                <div class="search-result__body">
                                    <span class="search-result__type">Documento · {{ $d->tenant?->short_name }}</span>
                                    <h3>{!! \App\Support\SearchHighlight::apply($d->title, $q) !!}</h3>
                                    @if($d->description)
                                        <p>{!! \App\Support\SearchHighlight::apply(\Illuminate\Support\Str::limit($d->description, 180), $q) !!}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endif

            @if($tab === 'all' || $tab === 'services')
                @if($results['services']->count() > 0)
                    <h2 class="search-group-title">Serviços</h2>
                    <div class="search-results">
                        @foreach($results['services'] as $s)
                            <a href="/servicos/{{ $s->slug }}" class="search-result" style="--accent:{{ $tenant->accent_color }}">
                                <div class="search-result__icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12h6M12 9v6"/></svg>
                                </div>
                                <div class="search-result__body">
                                    <span class="search-result__type">Serviço · {{ $tenant->short_name }}</span>
                                    <h3>{!! \App\Support\SearchHighlight::apply($s->title, $q) !!}</h3>
                                    @if($s->summary)<p>{!! \App\Support\SearchHighlight::apply($s->summary, $q) !!}</p>@endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endif

            @if($tab === 'all' || $tab === 'people')
                @if($results['people']->count() > 0)
                    <h2 class="search-group-title">Pessoas</h2>
                    <div class="search-results">
                        @foreach($results['people'] as $p)
                            @php $accent = $p->tenant?->accent_color ?? '#B92828'; @endphp
                            <div class="search-result" style="--accent:{{ $accent }}">
                                <div class="search-result__icon">
                                    @if($p->photo_path)
                                        <img src="{{ asset('storage/' . $p->photo_path) }}" alt="{{ $p->name }}" width="40" height="40" loading="lazy">
                                    @else
                                        <span class="search-result__initials">{{ collect(explode(' ', $p->name))->take(2)->map(fn ($x) => mb_substr($x, 0, 1))->join('') }}</span>
                                    @endif
                                </div>
                                <div class="search-result__body">
                                    <span class="search-result__type">{{ $p->tenant?->short_name }} · {{ $p->orgUnit?->name }}</span>
                                    <h3>{!! \App\Support\SearchHighlight::apply($p->name, $q) !!}</h3>
                                    <p>{!! \App\Support\SearchHighlight::apply($p->role_title, $q) !!}</p>
                                    @if($p->email)<small>{{ $p->email }}</small>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        @elseif($q)
            <div class="search-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <h2>Nenhum resultado para "{{ $q }}"</h2>
                <p>Verifique a ortografia ou tente palavras-chave mais genéricas.</p>
                <a href="/" class="btn btn--primary">Voltar à Home</a>
            </div>
        @else
            <div class="search-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <h2>Digite o que procura</h2>
                <p>A busca cobre notícias, documentos, serviços e pessoas da {{ $tenant->is_root ? 'PR-6' : $tenant->short_name }}.</p>
            </div>
        @endif

    </div>
</section>
@endsection
