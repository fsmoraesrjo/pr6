@extends('layouts.site')

@section('title', $item->title)

@php
    $accent = $item->tenant?->accent_color ?? '#B92828';
    $accentDeep = $item->tenant?->accent_deep_color ?? '#8E1B1B';
    $accentSoft = $item->tenant?->accent_soft_color ?? '#FCE4E5';
    $shareUrl = url()->current();
    $shareTitle = $item->title;
@endphp

@section('content')
<article class="article">
    <header class="article__head"
            @if($item->cover_path)
                style="background-image: linear-gradient(135deg, {{ $accent }}cc, {{ $accentDeep }}cc), url('{{ asset('storage/' . $item->cover_path) }}'); background-size: cover; background-position: center;"
            @else
                style="background-image: linear-gradient(135deg, {{ $accent }}, {{ $accentDeep }})"
            @endif>
        <div class="container">
            <nav class="article__breadcrumb" aria-label="Caminho">
                <a href="/">Início</a>
                <span aria-hidden="true">›</span>
                <a href="/noticias">Notícias</a>
                <span aria-hidden="true">›</span>
                <span>{{ \Illuminate\Support\Str::limit($item->title, 40) }}</span>
            </nav>
            @if($item->tenant)
                <a href="{{ $item->tenant->url() }}" class="article__badge">{{ $item->tenant->short_name }}</a>
            @endif
            @if($item->category)
                <a href="/noticias?categoria={{ $item->category->slug }}" class="article__category">{{ $item->category->name }}</a>
            @endif
            <h1 class="article__title">{{ $item->title }}</h1>
            @if($item->summary)
                <p class="article__lead">{{ $item->summary }}</p>
            @endif
            <div class="article__meta">
                <time>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ $item->published_at?->translatedFormat('d \d\e F \d\e Y') }}
                </time>
                @if($item->author)
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        {{ $item->author->name }}
                    </span>
                @endif
                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    {{ number_format($item->views_count, 0, ',', '.') }} {{ $item->views_count === 1 ? 'visualização' : 'visualizações' }}
                </span>
            </div>
        </div>
    </header>

    <div class="container article__layout">
        <aside class="article__share" aria-label="Compartilhar">
            <span class="article__share-label">Compartilhar</span>
            <a href="https://wa.me/?text={{ urlencode($shareTitle . ' ' . $shareUrl) }}"
               target="_blank" rel="noopener" aria-label="Compartilhar no WhatsApp" title="WhatsApp">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.6 6.3A8 8 0 0 0 4 14a8 8 0 0 0 1.1 4l-1.1 4 4.1-1.1a8 8 0 0 0 11.6-7 8 8 0 0 0-2.1-7.6zm-5.6 12.3a6.7 6.7 0 0 1-3.4-.9l-.2-.1-2.4.6.6-2.3-.2-.3a6.6 6.6 0 0 1 5.5-10A6.6 6.6 0 0 1 18.6 12a6.7 6.7 0 0 1-6.6 6.6zm3.6-5l-1.1-.6c-.2 0-.3 0-.4.2l-.6.7c-.1.1-.2.1-.4 0a5.4 5.4 0 0 1-2.7-2.4c-.2-.4 0-.4 0-.4l.4-.4c.1 0 .1-.2.2-.3 0-.1 0-.2 0-.3l-.5-1.3c-.2-.4-.4-.3-.5-.4h-.4l-.4.2c-.2 0-.5.3-.6.7-.2.4-.2 1.1.2 1.7a6 6 0 0 0 3 2.6c.4.2.7.3.9.4.4.1.7.1 1 0 .3 0 .9-.4 1-.7 0-.3 0-.6 0-.7 0-.1-.1-.1-.2-.2z"/></svg>
            </a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}"
               target="_blank" rel="noopener" aria-label="Compartilhar no LinkedIn" title="LinkedIn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14M8.3 18V10H5.7v8h2.6M7 8.6a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M19 18v-4.4c0-2.4-1.3-3.5-3-3.5-1.4 0-2 .8-2.4 1.3V10H11v8h2.6v-4.5c0-1 .2-2 1.5-2s1.4 1.2 1.4 2V18H19"/></svg>
            </a>
            <a href="mailto:?subject={{ urlencode($shareTitle) }}&body={{ urlencode($shareUrl) }}"
               aria-label="Compartilhar por e-mail" title="E-mail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
            </a>
            <button type="button" data-copy-url aria-label="Copiar link" title="Copiar link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/></svg>
            </button>
        </aside>

        <div class="article__body">
            @if($item->body)
                {!! $item->body !!}
            @else
                <p>{{ $item->summary }}</p>
            @endif
        </div>
    </div>
</article>

@if($related->isNotEmpty())
<section class="news-feed">
    <div class="container">
        <header class="section-head section-head--row">
            <h2 class="section-head__title">Mais notícias da {{ $item->tenant?->short_name ?? 'PR-6' }}</h2>
            <a href="/noticias" class="btn btn--ghost">Todas as notícias</a>
        </header>
        <div class="news-feed__grid">
            @foreach($related as $r)
                <article class="news" style="--accent:{{ $accent }}">
                    <a href="/noticias/{{ $r->slug }}" style="display: contents">
                        <div class="news__cover"
                             @if($r->cover_path)
                                 style="background-image: linear-gradient(135deg, {{ $accent }}aa, {{ $accentDeep }}aa), url('{{ asset('storage/' . $r->cover_path) }}'); background-size: cover; background-position: center;"
                             @else
                                 style="background-image: linear-gradient(135deg,{{ $accent }},{{ $accentDeep }})"
                             @endif>
                            <span class="news__badge">{{ $r->published_at?->translatedFormat('d \d\e M') }}</span>
                        </div>
                        <div class="news__body">
                            <h3 class="news__title">{{ $r->title }}</h3>
                            @if($r->summary)<p class="news__excerpt">{{ \Illuminate\Support\Str::limit($r->summary, 120) }}</p>@endif
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@push('scripts')
<script>
document.querySelectorAll('[data-copy-url]').forEach(btn => {
    btn.addEventListener('click', () => {
        navigator.clipboard.writeText(window.location.href);
        const original = btn.innerHTML;
        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l5 5L20 7"/></svg>';
        btn.style.color = '#15803D';
        setTimeout(() => { btn.innerHTML = original; btn.style.color = ''; }, 1500);
    });
});
</script>
@endpush

@endsection
