@extends('layouts.site')

@section('title', $document->title . ' · Documentos')

@php
    $accent = $document->tenant?->accent_color ?? '#B92828';
    $accentDeep = $document->tenant?->accent_deep_color ?? '#8E1B1B';
    $accentSoft = $document->tenant?->accent_soft_color ?? '#FCE4E5';
    $current = $document->currentVersion;
    $versions = $document->versions->sortByDesc('created_at');
@endphp

@section('content')

<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">
            @if($document->tenant){{ $document->tenant->short_name }} · @endif
            @if($document->category){{ $document->category->name }} ·@endif
            Documento
        </span>
        <h1 class="page-head__title">{{ $document->title }}</h1>
        @if($document->description)
            <p class="page-head__lead">{{ $document->description }}</p>
        @endif
        <div class="doc-show__meta">
            @if($document->published_at)
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                Publicado em {{ $document->published_at->format('d/m/Y') }}</span>
            @endif
            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15V3M5 12l7 7 7-7M5 21h14"/></svg>
            {{ number_format($document->downloads_count, 0, ',', '.') }} downloads</span>
            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
            {{ $versions->count() }} {{ $versions->count() === 1 ? 'versão' : 'versões' }}</span>
        </div>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="doc-show">
    <div class="container doc-show__inner">
        <div class="doc-show__main">
            <div class="doc-show__current" style="--accent:{{ $accent }};--accent-soft:{{ $accentSoft }};--accent-deep:{{ $accentDeep }}">
                <div class="doc-show__current-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                </div>
                <div class="doc-show__current-body">
                    <span class="doc-show__current-tag">Versão vigente</span>
                    @if($current)
                        <h2>{{ $current->version_label }}</h2>
                        <p>{{ $current->original_name ?? 'Arquivo institucional' }}</p>
                        <small>
                            @if($current->size_bytes)
                                {{ number_format($current->size_bytes / 1024, 0, ',', '.') }} KB ·
                            @endif
                            Atualizado em {{ $current->created_at->format('d/m/Y') }}
                        </small>
                    @else
                        <h2>Em preparação</h2>
                        <p>Nenhuma versão publicada ainda.</p>
                    @endif
                </div>
                @if($current)
                    <a href="{{ route('documents.download', ['slug' => $document->slug, 'versionId' => $current->id]) }}"
                       class="btn btn--primary doc-show__download"
                       style="background:linear-gradient(135deg, {{ $accent }}, {{ $accentDeep }})">
                        Baixar
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15V3M5 12l7 7 7-7M5 21h14"/></svg>
                    </a>
                @endif
            </div>

            @if($versions->count() > 1)
                <h3 class="doc-show__section-title">Histórico de versões</h3>
                <ol class="doc-timeline">
                    @foreach($versions as $v)
                        <li class="doc-timeline__item {{ $v->is_current ? 'is-current' : '' }}" style="--accent:{{ $accent }}">
                            <div class="doc-timeline__dot" aria-hidden="true"></div>
                            <div class="doc-timeline__content">
                                <header>
                                    <strong>{{ $v->version_label }}</strong>
                                    @if($v->is_current)<span class="doc-timeline__badge">vigente</span>@endif
                                    <time>{{ $v->created_at->format('d/m/Y') }}</time>
                                </header>
                                @if($v->changelog)
                                    <p>{{ $v->changelog }}</p>
                                @endif
                                <div class="doc-timeline__footer">
                                    @if($v->original_name)<small>{{ $v->original_name }}</small>@endif
                                    @if($v->size_bytes)<small>· {{ number_format($v->size_bytes / 1024, 0, ',', '.') }} KB</small>@endif
                                    <a href="{{ route('documents.download', ['slug' => $document->slug, 'versionId' => $v->id]) }}" class="doc-timeline__download">
                                        Baixar versão
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15V3M5 12l7 7 7-7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>

        <aside class="doc-show__side">
            @if($document->tenant)
                <div class="doc-show__side-card" style="--accent:{{ $accent }}">
                    <small>Origem</small>
                    <strong>{{ $document->tenant->short_name }}</strong>
                    <p>{{ $document->tenant->full_name }}</p>
                </div>
            @endif
            @if($document->category)
                <div class="doc-show__side-card">
                    <small>Categoria</small>
                    <strong>{{ $document->category->name }}</strong>
                    <a href="{{ route('documents.index') }}?categoria={{ $document->category->slug }}">Ver outros documentos da categoria</a>
                </div>
            @endif
            <div class="doc-show__side-card">
                <small>Compartilhar</small>
                <div class="doc-show__share">
                    <a href="mailto:?subject={{ urlencode($document->title) }}&body={{ urlencode(url()->current()) }}" aria-label="Compartilhar por e-mail">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4zM4 8l8 5 8-5"/></svg>
                    </a>
                    <button type="button" data-copy-url aria-label="Copiar link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/></svg>
                    </button>
                </div>
            </div>
        </aside>
    </div>
</section>

@push('scripts')
<script>
document.querySelectorAll('[data-copy-url]').forEach(btn => {
    btn.addEventListener('click', () => {
        navigator.clipboard.writeText(window.location.href);
        const original = btn.innerHTML;
        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l5 5L20 7"/></svg>';
        setTimeout(() => btn.innerHTML = original, 1500);
    });
});
</script>
@endpush

@endsection
