@extends('layouts.site')

@section('title', 'Direitos do titular · LGPD · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · LGPD · Lei 13.709/2018</span>
        <h1 class="page-head__title">Direitos do titular dos dados</h1>
        <p class="page-head__lead">Solicite acesso, correção, portabilidade ou exclusão dos seus dados pessoais. Respondemos em até 15 dias com sigilo garantido.</p>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="lgpd-page">
    <div class="container lgpd-page__inner lgpd-page__inner--rights">

        <aside class="lgpd-page__rights-info">
            <h3>O que você pode pedir</h3>
            <ul class="lgpd-rights-list">
                <li>
                    <strong>Acesso</strong>
                    <span>Saber quais dados pessoais a UERJ trata sobre você.</span>
                </li>
                <li>
                    <strong>Correção</strong>
                    <span>Solicitar atualização de dados incompletos, inexatos ou desatualizados.</span>
                </li>
                <li>
                    <strong>Exclusão</strong>
                    <span>Pedir a remoção de dados tratados com base em consentimento.</span>
                </li>
                <li>
                    <strong>Portabilidade</strong>
                    <span>Receber seus dados em formato estruturado para enviar a outro serviço.</span>
                </li>
                <li>
                    <strong>Revogação de consentimento</strong>
                    <span>Cancelar autorizações que você havia dado anteriormente.</span>
                </li>
                <li>
                    <strong>Oposição ao tratamento</strong>
                    <span>Manifestar-se contra tratamento que entenda em desacordo com a LGPD.</span>
                </li>
            </ul>

            <div class="lgpd-page__deadline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                <div>
                    <strong>Prazo legal: até 15 dias</strong>
                    <p>A resposta é enviada para o e-mail informado, com sigilo. Em casos de complexidade, podemos prorrogar o prazo conforme a LGPD.</p>
                </div>
            </div>

            <div class="lgpd-page__dpo">
                <strong>Encarregado de dados (DPO)</strong>
                <p>E-mail: <a href="mailto:dpo@uerj.br">dpo@uerj.br</a></p>
                <p>Para dúvidas sobre privacidade ou denúncias.</p>
            </div>
        </aside>

        <div class="lgpd-page__form-wrapper">
            @if(session('lgpd_submitted'))
                <div class="lgpd-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12l5 5L20 7"/></svg>
                    <div>
                        <strong>Solicitação registrada com sucesso</strong>
                        <p>Recebemos sua solicitação e entraremos em contato pelo e-mail informado em até 15 dias.</p>
                        <p>Guarde este número de protocolo: <code>LGPD-{{ now()->format('Ymd') }}-{{ strtoupper(substr(md5(session()->getId()), 0, 8)) }}</code></p>
                    </div>
                </div>
            @else
                <form method="POST" action="{{ route('lgpd.store') }}" class="lgpd-form" id="lgpd-form">
                    @csrf
                    <h2>Formulário de solicitação</h2>
                    <p class="lgpd-form__intro">Preencha com cuidado. Os dados aqui informados são <strong>criptografados em repouso</strong> e usados apenas para identificá-lo e responder.</p>

                    <div class="lgpd-form__row lgpd-form__row--two">
                        <label>
                            <span>Nome completo *</span>
                            <input type="text" name="name" required maxlength="160" value="{{ old('name') }}">
                        </label>
                        <label>
                            <span>E-mail para resposta *</span>
                            <input type="email" name="email" required value="{{ old('email') }}">
                        </label>
                    </div>

                    <label>
                        <span>CPF *</span>
                        <input type="text" name="cpf" required maxlength="14" placeholder="000.000.000-00" value="{{ old('cpf') }}" data-mask="cpf">
                        <small>Necessário para confirmar sua identidade. Será criptografado e nunca exibido em texto puro no admin.</small>
                    </label>

                    <fieldset class="lgpd-form__types">
                        <legend>Tipo de solicitação *</legend>
                        @foreach(\App\Models\DataSubjectRequest::REQUEST_TYPES as $key => $label)
                            <label class="lgpd-form__type">
                                <input type="radio" name="request_type" value="{{ $key }}" {{ old('request_type') === $key ? 'checked' : '' }} required>
                                <div>
                                    <strong>{{ $label }}</strong>
                                    <span>{{ match($key) {
                                        'acesso' => 'Quero saber quais dados a UERJ tem sobre mim',
                                        'correcao' => 'Algum dado meu está errado e preciso corrigir',
                                        'exclusao' => 'Quero que excluam meus dados (tratados sob consentimento)',
                                        'portabilidade' => 'Quero meus dados em formato exportável',
                                        'revogacao_consentimento' => 'Quero revogar autorizações que dei',
                                        'oposicao' => 'Quero me opor a algum uso dos meus dados',
                                    } }}</span>
                                </div>
                            </label>
                        @endforeach
                    </fieldset>

                    <label>
                        <span>Descreva sua solicitação *</span>
                        <textarea name="description" rows="6" required minlength="20" maxlength="2000">{{ old('description') }}</textarea>
                        <small>Descreva com clareza o que você está solicitando. Se possível, indique datas, serviços ou contexto.</small>
                    </label>

                    @if($errors->any())
                        <div class="lgpd-form__errors">
                            @foreach($errors->all() as $err)
                                <p>{{ $err }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="lgpd-form__consent">
                        <label>
                            <input type="checkbox" name="consent" required>
                            <span>Concordo que a UERJ use os dados aqui informados <strong>exclusivamente para responder a esta solicitação</strong>, conforme a <a href="/privacidade">Política de Privacidade</a>.</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn--primary lgpd-form__submit">
                        Enviar solicitação
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </button>
                </form>
            @endif
        </div>

    </div>
</section>

@push('scripts')
<script>
document.querySelectorAll('[data-mask="cpf"]').forEach(input => {
    input.addEventListener('input', () => {
        let v = input.value.replace(/\D/g, '').slice(0, 11);
        if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
        else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
        else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
        input.value = v;
    });
});
</script>
@endpush

@endsection
