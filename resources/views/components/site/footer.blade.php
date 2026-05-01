@props(['tenant'])

@php
    $verticals = \App\Models\Tenant::query()->where('is_root', false)->where('is_active', true)->orderBy('order')->get();
@endphp

<footer class="footer" role="contentinfo">
    <div class="container footer__top">
        <div class="footer__brand">
            <img src="{{ asset('assets/logo-pr6-branca.png') }}" alt="PR-6 · Pró-Reitoria de Planejamento e Gestão" class="footer__logo">
            <p>{{ $tenant->full_name }}. Universidade do Estado do Rio de Janeiro.</p>
        </div>
        <div class="footer__col">
            <h4>Diretorias</h4>
            <ul>
                @foreach($verticals as $v)
                    <li><a href="{{ $v->url() }}">{{ $v->short_name }}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="footer__col">
            <h4>Institucional</h4>
            <ul>
                <li><a href="/sobre">Sobre</a></li>
                <li><a href="/equipe">Equipe e contatos</a></li>
                <li><a href="/organograma">Organograma</a></li>
                <li><a href="/documentos">Documentos</a></li>
            </ul>
        </div>
        <div class="footer__col">
            <h4>Transparência</h4>
            <ul>
                <li><a href="/indicadores">Indicadores</a></li>
                <li><a href="/privacidade">Política de privacidade</a></li>
                <li><a href="/lgpd">Encarregado de dados</a></li>
                <li><a href="/acessibilidade">Acessibilidade</a></li>
            </ul>
        </div>
        <div class="footer__col">
            <h4>Contato</h4>
            <address>
                Rua São Francisco Xavier, 524<br>
                Maracanã, Rio de Janeiro/RJ<br>
                <a href="mailto:pr6@uerj.br">pr6@uerj.br</a>
            </address>
        </div>
    </div>
    <div class="container footer__bottom">
        <small>© {{ date('Y') }} UERJ · {{ $tenant->full_name }}</small>
        <small>Portal PR-6 · v0.1</small>
    </div>
</footer>
