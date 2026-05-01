@extends('layouts.site')

@section('title', 'Notícias · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Comunicação</span>
        <h1 class="page-head__title">Notícias{{ $tenant->is_root ? '' : ' da ' . $tenant->short_name }}</h1>
        <p class="page-head__lead">Acompanhe os comunicados, decisões e novidades publicadas pela {{ $tenant->is_root ? 'PR-6' : $tenant->short_name }}.</p>
        <form class="page-head__search" method="get">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar notícias..." aria-label="Buscar">
            <button type="submit" aria-label="Buscar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            </button>
        </form>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="news-list">
    <div class="container">
        @if($news->isEmpty())
            <p class="empty-state">Nenhuma notícia encontrada.</p>
        @else
            <div class="news-feed__grid news-feed__grid--list">
                @foreach($news as $i => $n)
                    @php
                        $accent = $n->tenant?->accent_color ?? ($tenant->accent_color);
                        $accentDeep = $n->tenant?->accent_deep_color ?? ($tenant->accent_deep_color);
                    @endphp
                    <article class="news" style="--accent:{{ $accent }}">
                        <div class="news__cover" style="background-image:linear-gradient(135deg,{{ $accent }},{{ $accentDeep }})">
                            @if($n->tenant)<span class="news__badge">{{ $n->tenant->short_name }}</span>@endif
                        </div>
                        <div class="news__body">
                            <time class="news__date">{{ $n->published_at?->translatedFormat('d \d\e F, Y') }}</time>
                            <h3 class="news__title"><a href="/noticias/{{ $n->slug }}">{{ $n->title }}</a></h3>
                            <p class="news__excerpt">{{ $n->summary }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="pagination-wrapper">{{ $news->links() }}</div>
        @endif
    </div>
</section>
@endsection
