@extends('layouts.site')

@section('title', $titulo . ' · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }}</span>
        <h1 class="page-head__title">{{ $titulo }}</h1>
        <p class="page-head__lead">Esta seção está em construção. Em breve, todo o conteúdo de {{ $titulo }} estará disponível aqui.</p>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>
<section style="padding: clamp(2rem, 5vw, 4rem) 0;">
    <div class="container">
        <a href="/" class="btn btn--primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Voltar à Home
        </a>
    </div>
</section>
@endsection
