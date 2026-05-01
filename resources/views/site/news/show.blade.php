@extends('layouts.site')

@section('title', $item->title)

@php
    $accent = $item->tenant?->accent_color ?? '#B92828';
    $accentDeep = $item->tenant?->accent_deep_color ?? '#8E1B1B';
@endphp

@section('content')
<article class="article">
    <header class="article__head" style="background-image:linear-gradient(135deg,{{ $accent }},{{ $accentDeep }})">
        <div class="container">
            @if($item->tenant)
                <a href="{{ $item->tenant->url() }}" class="article__badge">{{ $item->tenant->short_name }}</a>
            @endif
            <h1 class="article__title">{{ $item->title }}</h1>
            <p class="article__lead">{{ $item->summary }}</p>
            <div class="article__meta">
                <time>{{ $item->published_at?->translatedFormat('d \d\e F \d\e Y') }}</time>
                @if($item->author) · {{ $item->author->name }} @endif
            </div>
        </div>
    </header>
    <div class="container article__body">
        {!! $item->body !!}
    </div>
</article>

@if($related->isNotEmpty())
<section class="news-feed">
    <div class="container">
        <header class="section-head section-head--row">
            <h2 class="section-head__title">Mais notícias</h2>
        </header>
        <div class="news-feed__grid">
            @foreach($related as $r)
                <article class="news" style="--accent:{{ $accent }}">
                    <div class="news__cover" style="background-image:linear-gradient(135deg,{{ $accent }},{{ $accentDeep }})"></div>
                    <div class="news__body">
                        <time class="news__date">{{ $r->published_at?->translatedFormat('d/m/Y') }}</time>
                        <h3 class="news__title"><a href="/noticias/{{ $r->slug }}">{{ $r->title }}</a></h3>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
