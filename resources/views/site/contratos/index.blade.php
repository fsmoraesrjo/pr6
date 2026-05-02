@extends('layouts.site')

@section('title', 'Contratos · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Transparência</span>
        <h1 class="page-head__title">Contratos</h1>
        <p class="page-head__lead">
            Contratos administrativos vigentes da {{ $tenant->is_root ? 'PR-6' : $tenant->short_name }},
            consultados em tempo real pelo sistema de Controle de Contratos.
        </p>
        <div class="page-head__cta">
            <a href="https://cc.pr6.lumislabs.com.br/publico/contratos" target="_blank" rel="noopener" class="btn btn--gold">
                Abrir em nova aba
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 4h6v6M10 14L20 4M19 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6"/></svg>
            </a>
        </div>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="contratos-iframe">
    <div class="container">
        <div class="contratos-iframe__wrap">
            <iframe
                src="https://cc.pr6.lumislabs.com.br/publico/contratos"
                title="Sistema de Controle de Contratos da PR-6"
                loading="lazy"
                referrerpolicy="strict-origin-when-cross-origin"
                allow="fullscreen"
                sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-popups-to-escape-sandbox"
                class="contratos-iframe__frame"></iframe>
        </div>
        <p class="contratos-iframe__credit">
            Dados fornecidos pelo
            <a href="https://cc.pr6.lumislabs.com.br" target="_blank" rel="noopener">Sistema CC · Controle de Contratos</a>
            da PR-6 UERJ.
        </p>
    </div>
</section>

@push('head')
<style>
    .contratos-iframe { padding: clamp(1.5rem, 4vw, 3rem) 0; }
    .contratos-iframe__wrap {
        position: relative;
        background: var(--paper-elev);
        border: 1px solid var(--line);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        min-height: 70vh;
    }
    .contratos-iframe__frame {
        display: block;
        width: 100%;
        height: 75vh;
        min-height: 600px;
        border: 0;
        background: var(--paper);
    }
    .contratos-iframe__credit {
        margin-top: 1rem;
        font-size: 12.5px;
        color: var(--ink-mute);
        text-align: center;
    }
    .contratos-iframe__credit a {
        color: var(--pr6-primary);
        font-weight: 600;
        text-decoration: underline;
    }
    @media (min-width: 1280px) {
        .contratos-iframe__frame { height: 80vh; }
    }
</style>
@endpush

@endsection
