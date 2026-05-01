@extends('layouts.site')

@section('title', 'Documentos · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Repositório institucional</span>
        <h1 class="page-head__title">Documentos{{ $tenant->is_root ? '' : ' da ' . $tenant->short_name }}</h1>
        <p class="page-head__lead">Atos, planos, relatórios e formulários publicados oficialmente pela {{ $tenant->is_root ? 'PR-6' : $tenant->short_name }}.</p>
        <form class="page-head__search" method="get">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar documentos..." aria-label="Buscar">
            <button type="submit" aria-label="Buscar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            </button>
        </form>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="docs-list">
    <div class="container docs-list__inner">
        <aside class="docs-list__filters">
            <h3>Categorias</h3>
            <ul>
                <li><a href="?" class="{{ !request('categoria') ? 'is-active' : '' }}">Todas</a></li>
                @foreach($categories as $c)
                    <li><a href="?categoria={{ $c->slug }}" class="{{ request('categoria') === $c->slug ? 'is-active' : '' }}">{{ $c->name }}</a></li>
                @endforeach
            </ul>
        </aside>

        <div class="docs-list__grid">
            @if($documents->isEmpty())
                <p class="empty-state">Nenhum documento publicado ainda.</p>
            @else
                @foreach($documents as $d)
                    @php $a = $d->tenant?->accent_color ?? '#B92828'; @endphp
                    <a href="{{ route('documents.show', $d->slug) }}" class="doc-card" style="--accent:{{ $a }}">
                        <div class="doc-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8"/></svg>
                        </div>
                        <div class="doc-card__body">
                            @if($d->tenant)<span class="doc-card__tenant">{{ $d->tenant->short_name }}</span>@endif
                            <h3>{{ $d->title }}</h3>
                            @if($d->category)<small>{{ $d->category->name }}</small>@endif
                            @if($d->description)<p>{{ \Illuminate\Support\Str::limit($d->description, 120) }}</p>@endif
                        </div>
                        <div class="doc-card__meta">
                            @if($d->currentVersion)<span class="doc-card__version">{{ $d->currentVersion->version_label }}</span>@endif
                            <span>{{ $d->downloads_count }} downloads</span>
                            <time>{{ $d->published_at?->format('d/m/Y') }}</time>
                        </div>
                    </a>
                @endforeach
                <div class="pagination-wrapper">{{ $documents->links() }}</div>
            @endif
        </div>
    </div>
</section>
@endsection
