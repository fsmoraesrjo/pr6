@extends('layouts.site')

@section('title', $service->title . ' · ' . $tenant->short_name)

@section('content')
<section class="page-head" style="background: linear-gradient(135deg, {{ $tenant->accent_color }}, {{ $tenant->accent_deep_color }}); color: #fff">
    <div class="container">
        <span class="section-head__eyebrow" style="color: rgba(255,255,255,.85)">Serviço · {{ $tenant->short_name }}</span>
        <h1 class="page-head__title" style="color:#fff">{{ $service->title }}</h1>
        @if($service->summary)<p class="page-head__lead" style="color:rgba(255,255,255,.9)">{{ $service->summary }}</p>@endif
    </div>
</section>

<section class="service-detail">
    <div class="container service-detail__inner">
        <div class="service-detail__main">
            @if($service->description){!! $service->description !!}@endif

            @if($service->requirements)
                <h3>Requisitos</h3>
                <ul>
                    @foreach((array) $service->requirements as $req)
                        <li>{{ $req }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        <aside class="service-detail__side">
            @if($service->audience)
                <p><strong>Público-alvo</strong><br>{{ $service->audience }}</p>
            @endif

            @switch($service->request_type)
                @case('internal_form')
                    <a href="/contato?servico={{ $service->slug }}" class="btn btn--primary" style="background:linear-gradient(135deg,{{ $tenant->accent_color }},{{ $tenant->accent_deep_color }});width:100%;justify-content:center">Solicitar serviço</a>
                    @break
                @case('external_url')
                    <a href="{{ $service->request_url }}" target="_blank" rel="noopener" class="btn btn--primary" style="background:linear-gradient(135deg,{{ $tenant->accent_color }},{{ $tenant->accent_deep_color }});width:100%;justify-content:center">Abrir solicitação</a>
                    @break
                @case('email')
                    <a href="mailto:{{ $service->request_email }}" class="btn btn--primary" style="background:linear-gradient(135deg,{{ $tenant->accent_color }},{{ $tenant->accent_deep_color }});width:100%;justify-content:center">Enviar e-mail</a>
                    @break
                @default
                    <a href="/contato" class="btn btn--secondary" style="width:100%;justify-content:center">Mais informações</a>
            @endswitch
        </aside>
    </div>
</section>
@endsection
