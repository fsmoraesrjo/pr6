@props(['tenant'])

<header class="header" role="banner">
    <div class="container header__inner header__inner--main">
        <a href="/" class="brand" aria-label="{{ $tenant->short_name }} · {{ $tenant->full_name }}">
            @if($tenant->is_root)
                <img src="{{ asset('assets/logo-pr6-cor.png') }}" alt="PR-6 · Pró-Reitoria de Planejamento e Gestão" class="brand__logo">
            @else
                <img src="{{ asset('assets/logo-pr6-cor.png') }}" alt="PR-6" class="brand__logo brand__logo--small">
                <span class="brand__divider" aria-hidden="true"></span>
                <span class="brand__text">
                    <strong>{{ $tenant->short_name }}</strong>
                    <small>{{ $tenant->tagline }}</small>
                </span>
            @endif
        </a>

        @php
            $transparenciaActive = request()->is('documentos*')
                || request()->is('agenda*')
                || request()->is('contratos*')
                || request()->is('indicadores*')
                || request()->is('pessoas*')
                || request()->is('equipe*')
                || request()->is('transparencia*');
        @endphp
        <nav class="nav" aria-label="Navegação principal">
            <ul>
                <li><a href="/" class="{{ request()->is('/') ? 'is-active' : '' }}">Início</a></li>
                <li><a href="/sobre" class="{{ request()->is('sobre*') ? 'is-active' : '' }}">Institucional</a></li>
                <li class="nav__has-children">
                    <button type="button" class="nav__trigger {{ $transparenciaActive ? 'is-active' : '' }}" aria-haspopup="true" aria-expanded="false">
                        Transparência
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 8l4 4 4-4"/></svg>
                    </button>
                    <div class="nav__dropdown" role="menu">
                        <div class="nav__dropdown-inner">
                            <a href="/documentos" role="menuitem">
                                <span class="nav__dropdown-label">Documentos</span>
                                <span class="nav__dropdown-desc">Atos, planos, relatórios e formulários publicados</span>
                            </a>
                            <a href="/agenda" role="menuitem">
                                <span class="nav__dropdown-label">Agenda</span>
                                <span class="nav__dropdown-desc">Reuniões, eventos e consultas públicas</span>
                            </a>
                            <a href="/contratos" role="menuitem">
                                <span class="nav__dropdown-label">Contratos</span>
                                <span class="nav__dropdown-desc">Contratos administrativos e termos vigentes</span>
                            </a>
                            <a href="/indicadores" role="menuitem">
                                <span class="nav__dropdown-label">Painel de indicadores</span>
                                <span class="nav__dropdown-desc">Execução de metas, orçamento e desempenho</span>
                            </a>
                            <a href="/pessoas" role="menuitem">
                                <span class="nav__dropdown-label">Pessoas</span>
                                <span class="nav__dropdown-desc">Equipe, estrutura organizacional e contatos</span>
                            </a>
                        </div>
                        <div class="nav__dropdown-stripe" aria-hidden="true"></div>
                    </div>
                </li>
                <li><a href="/noticias" class="{{ request()->is('noticias*') ? 'is-active' : '' }}">Notícias</a></li>
                @if(!$tenant->is_root)
                    <li><a href="/servicos" class="{{ request()->is('servicos*') ? 'is-active' : '' }}">Serviços</a></li>
                @endif
                <li><a href="/contato" class="{{ request()->is('contato*') || request()->is('fale-conosco*') ? 'is-active' : '' }}">Contato</a></li>
            </ul>
        </nav>

        <div class="header__tools">
            <button type="button" class="icon-btn" aria-label="Buscar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            </button>
            <button type="button" class="icon-btn" id="theme-toggle" aria-label="Alternar tema">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="theme-icon-light"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                <svg viewBox="0 0 24 24" fill="currentColor" class="theme-icon-dark"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
        </div>

        <button class="menu-toggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <x-site.portal-bar :current="$tenant" />
</header>
