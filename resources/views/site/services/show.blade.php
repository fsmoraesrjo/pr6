@extends('layouts.site')

@section('title', $service->title . ' · ' . $tenant->short_name)

@section('content')
<section class="page-head" style="background: linear-gradient(135deg, {{ $tenant->accent_color }}, {{ $tenant->accent_deep_color }}); color: #fff">
    <div class="container">
        <nav class="article__breadcrumb" aria-label="Caminho">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/servicos">Serviços</a>
            <span aria-hidden="true">›</span>
            <span>{{ \Illuminate\Support\Str::limit($service->title, 40) }}</span>
        </nav>
        <span class="section-head__eyebrow" style="color: rgba(255,255,255,.85)">Serviço · {{ $tenant->short_name }}</span>
        <h1 class="page-head__title" style="color:#fff">{{ $service->title }}</h1>
        @if($service->summary)<p class="page-head__lead" style="color:rgba(255,255,255,.92)">{{ $service->summary }}</p>@endif
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="service-detail">
    <div class="container service-detail__inner">
        <div class="service-detail__main">
            @if($service->description)
                <div class="service-detail__body">
                    {!! $service->description !!}
                </div>
            @endif

            @if($service->requirements && count((array) $service->requirements) > 0)
                <h3 class="service-detail__section-title">Requisitos</h3>
                <ul class="service-detail__requirements">
                    @foreach((array) $service->requirements as $req)
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12l5 5L20 7"/></svg>
                            {{ is_array($req) ? ($req['value'] ?? '') : $req }}
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($service->request_type === 'internal_form')
                <h3 class="service-detail__section-title" id="solicitar">Solicitar este serviço</h3>
                @if(session('service_submitted'))
                    <div class="service-form-success" style="--accent: {{ $tenant->accent_color }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12l5 5L20 7"/></svg>
                        <div>
                            <strong>Solicitação enviada com sucesso!</strong>
                            <p>A {{ $tenant->short_name }} responderá em até 5 dias úteis no e-mail informado.</p>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('services.submit', $service->slug) }}" class="service-form" style="--accent: {{ $tenant->accent_color }}">
                        @csrf
                        <div class="service-form__row service-form__row--two">
                            <label>
                                <span>Nome completo *</span>
                                <input type="text" name="name" required maxlength="160" value="{{ old('name') }}">
                            </label>
                            <label>
                                <span>E-mail *</span>
                                <input type="email" name="email" required value="{{ old('email') }}">
                            </label>
                        </div>
                        <div class="service-form__row service-form__row--two">
                            <label>
                                <span>Telefone (opcional)</span>
                                <input type="tel" name="phone" maxlength="32" value="{{ old('phone') }}" placeholder="(21) 99999-9999">
                            </label>
                            <label>
                                <span>Vínculo com a UERJ</span>
                                <select name="subject">
                                    <option value="docente">Docente</option>
                                    <option value="tecnico">Servidor técnico-administrativo</option>
                                    <option value="aluno">Aluno</option>
                                    <option value="terceirizado">Terceirizado</option>
                                    <option value="externo">Público externo</option>
                                </select>
                            </label>
                        </div>
                        <label>
                            <span>Descrição da solicitação *</span>
                            <textarea name="message" rows="5" required minlength="20" maxlength="2000">{{ old('message') }}</textarea>
                            <small>Mínimo de 20 caracteres. Descreva o serviço desejado, o local (se aplicável) e a urgência.</small>
                        </label>
                        @error('name')<p class="service-form__error">{{ $message }}</p>@enderror
                        @error('email')<p class="service-form__error">{{ $message }}</p>@enderror
                        @error('message')<p class="service-form__error">{{ $message }}</p>@enderror
                        <div class="service-form__lgpd">
                            <label>
                                <input type="checkbox" name="consent" required>
                                <span>Concordo com o tratamento dos meus dados conforme a <a href="/privacidade">Política de Privacidade</a> da PR-6.</span>
                            </label>
                        </div>
                        <button type="submit" class="btn btn--primary"
                                style="background:linear-gradient(135deg,{{ $tenant->accent_color }},{{ $tenant->accent_deep_color }});">
                            Enviar solicitação
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                        </button>
                    </form>
                @endif
            @endif
        </div>

        <aside class="service-detail__side">
            <div class="service-detail__card" style="--accent: {{ $tenant->accent_color }}">
                @if($service->audience)
                    <small>Público-alvo</small>
                    <p><strong>{{ $service->audience }}</strong></p>
                @endif
                @if($service->category)
                    <small>Categoria</small>
                    <p>{{ $service->category->name }}</p>
                @endif
                <small>Como solicitar</small>
                <p>{{ match ($service->request_type) {
                    'internal_form' => 'Pelo formulário ao lado',
                    'external_url' => 'Por sistema externo',
                    'email' => 'Por e-mail',
                    'info_only' => 'Informativo (sem solicitação)',
                    default => 'Consulte a equipe',
                } }}</p>
            </div>

            @switch($service->request_type)
                @case('internal_form')
                    <a href="#solicitar" class="btn btn--primary"
                       style="background:linear-gradient(135deg,{{ $tenant->accent_color }},{{ $tenant->accent_deep_color }});width:100%;justify-content:center">
                        Ir para o formulário
                    </a>
                    @break
                @case('external_url')
                    <a href="{{ $service->request_url }}" target="_blank" rel="noopener" class="btn btn--primary"
                       style="background:linear-gradient(135deg,{{ $tenant->accent_color }},{{ $tenant->accent_deep_color }});width:100%;justify-content:center">
                        Abrir sistema externo
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 4h6v6M10 14L20 4M19 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6"/></svg>
                    </a>
                    @break
                @case('email')
                    <a href="mailto:{{ $service->request_email }}" class="btn btn--primary"
                       style="background:linear-gradient(135deg,{{ $tenant->accent_color }},{{ $tenant->accent_deep_color }});width:100%;justify-content:center">
                        Enviar e-mail
                    </a>
                    @break
                @default
                    <a href="/contato" class="btn btn--secondary" style="width:100%;justify-content:center">Mais informações</a>
            @endswitch
        </aside>
    </div>
</section>
@endsection
