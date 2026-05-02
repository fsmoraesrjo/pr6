@extends('layouts.site')

@section('title', 'Política de privacidade · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · LGPD</span>
        <h1 class="page-head__title">Política de privacidade</h1>
        <p class="page-head__lead">Como a {{ $tenant->is_root ? 'PR-6' : $tenant->short_name }} coleta, usa, armazena e protege seus dados pessoais, em conformidade com a Lei nº 13.709/2018 (LGPD).</p>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="lgpd-page">
    <div class="container lgpd-page__inner">

        <aside class="lgpd-page__toc" aria-label="Índice">
            <strong>Nesta página</strong>
            <ol>
                <li><a href="#identificacao">Quem somos</a></li>
                <li><a href="#dados">Dados que coletamos</a></li>
                <li><a href="#bases">Bases legais</a></li>
                <li><a href="#uso">Como usamos</a></li>
                <li><a href="#compartilhamento">Compartilhamento</a></li>
                <li><a href="#cookies">Cookies</a></li>
                <li><a href="#armazenamento">Armazenamento e segurança</a></li>
                <li><a href="#direitos">Seus direitos</a></li>
                <li><a href="#dpo">Encarregado de dados</a></li>
                <li><a href="#alteracoes">Alterações</a></li>
            </ol>
            <p class="lgpd-page__updated">Atualizada em {{ now()->translatedFormat('F \d\e Y') }}.</p>
        </aside>

        <article class="lgpd-page__content">
            <h2 id="identificacao">1. Quem somos</h2>
            <p>A Pró-Reitoria de Planejamento e Gestão (PR-6) é unidade administrativa da Universidade do Estado do Rio de Janeiro (UERJ), inscrita no CNPJ 33.540.014/0001-57, com sede na Rua São Francisco Xavier, 524, Maracanã, Rio de Janeiro/RJ.</p>
            <p>Esta política aplica-se a todo o portal <strong>{{ $tenant->is_root ? 'pr6.uerj.br' : $tenant->slug . '.pr6.uerj.br' }}</strong> e às quatro diretorias subordinadas: DIRTEC, DIRGIS, DIRPLAG e COOMAS.</p>

            <h2 id="dados">2. Dados que coletamos</h2>
            <p>Coletamos apenas o estritamente necessário para cada finalidade. Dependendo da sua interação com o portal, podemos receber:</p>
            <ul>
                <li><strong>Dados de navegação</strong>: IP (armazenado em hash SHA-256), tipo de navegador, páginas visitadas e duração da visita.</li>
                <li><strong>Dados de contato</strong>: nome, e-mail e telefone (quando você usa formulários de fale conosco ou solicitação de serviço).</li>
                <li><strong>Dados de identificação</strong>: CPF (somente quando exercendo direitos do titular previstos em lei), criptografado em repouso.</li>
                <li><strong>Conteúdo voluntário</strong>: mensagens, descrições e anexos que você decide enviar pelos formulários.</li>
            </ul>

            <h2 id="bases">3. Bases legais para o tratamento</h2>
            <p>Tratamos seus dados pessoais com base em uma das hipóteses legais previstas pela LGPD:</p>
            <ul>
                <li><strong>Execução de políticas públicas</strong> (art. 7º, III): para o cumprimento das atribuições legais da PR-6 e da UERJ.</li>
                <li><strong>Cumprimento de obrigação legal ou regulatória</strong> (art. 7º, II): registros de transparência ativa, prestação de contas, contratos administrativos.</li>
                <li><strong>Consentimento</strong> (art. 7º, I): cookies analíticos, newsletters e pesquisas opcionais.</li>
                <li><strong>Legítimo interesse</strong> (art. 7º, IX): segurança da informação, prevenção de fraudes, melhoria do portal.</li>
            </ul>

            <h2 id="uso">4. Como usamos seus dados</h2>
            <ul>
                <li>Atender solicitações via formulários (fale conosco, serviços, ouvidoria).</li>
                <li>Cumprir prazos legais de resposta a manifestações.</li>
                <li>Garantir a segurança e disponibilidade do portal.</li>
                <li>Produzir indicadores agregados de transparência ativa.</li>
                <li>Melhorar a experiência de navegação (apenas com cookies analíticos consentidos).</li>
            </ul>

            <h2 id="compartilhamento">5. Compartilhamento</h2>
            <p>Não vendemos, alugamos ou comercializamos seus dados. Podemos compartilhar dados estritamente necessários com:</p>
            <ul>
                <li>Outras unidades da UERJ, quando a solicitação exigir tramitação interna.</li>
                <li>Órgãos de controle (TCE-RJ, MPRJ, CGU), quando legalmente requerido.</li>
                <li>Operadores contratados pela UERJ que oferecem suporte técnico ao portal, sob acordo de confidencialidade e nas condições da LGPD.</li>
            </ul>

            <h2 id="cookies">6. Cookies</h2>
            <p>Usamos três categorias de cookies, com consentimento separado:</p>
            <ul>
                <li><strong>Essenciais</strong>: indispensáveis para login, sessão e segurança. Sempre ativos.</li>
                <li><strong>Analíticos</strong>: medem audiência e comportamento agregado. Ativados apenas com seu consentimento.</li>
                <li><strong>Marketing</strong>: personalização de campanhas institucionais. Atualmente não utilizamos cookies de marketing.</li>
            </ul>
            <p>Você pode revisar e revogar seus consentimentos a qualquer momento limpando os cookies do navegador ou usando o formulário de revogação na <a href="/lgpd">página de direitos do titular</a>.</p>

            <h2 id="armazenamento">7. Armazenamento e segurança</h2>
            <p>Os dados pessoais são armazenados em servidores controlados pela UERJ, com:</p>
            <ul>
                <li>Criptografia em repouso (AES-256 via APP_KEY) para dados sensíveis como CPF e e-mail vinculado a solicitações de direitos do titular.</li>
                <li>Comunicação criptografada em trânsito (TLS 1.2+).</li>
                <li>Hash unidirecional (SHA-256) para IPs e identificadores de sessão.</li>
                <li>Controle de acesso baseado em papéis (RBAC) e registro de auditoria das ações administrativas.</li>
            </ul>
            <p>Mantemos os dados pelo prazo necessário ao cumprimento das finalidades, observados os prazos legais de guarda obrigatória.</p>

            <h2 id="direitos">8. Seus direitos como titular</h2>
            <p>A LGPD garante os seguintes direitos:</p>
            <ul>
                <li><strong>Acesso</strong> aos dados que tratamos sobre você.</li>
                <li><strong>Correção</strong> de dados incompletos, inexatos ou desatualizados.</li>
                <li><strong>Anonimização, bloqueio ou eliminação</strong> de dados desnecessários ou tratados em desconformidade.</li>
                <li><strong>Portabilidade</strong> dos dados a outro fornecedor de serviço.</li>
                <li><strong>Eliminação</strong> dos dados tratados com base em consentimento.</li>
                <li><strong>Informação sobre compartilhamento</strong> com entidades públicas e privadas.</li>
                <li><strong>Revogação do consentimento</strong> a qualquer momento.</li>
                <li><strong>Oposição</strong> ao tratamento que descumpra a LGPD.</li>
            </ul>
            <p>Para exercer qualquer desses direitos, utilize o <a href="/lgpd">formulário de solicitação</a>. O prazo legal de resposta é de até 15 (quinze) dias.</p>

            <h2 id="dpo">9. Encarregado de dados (DPO)</h2>
            <div class="lgpd-dpo">
                <strong>Encarregado pelo Tratamento de Dados Pessoais da UERJ</strong>
                <p>E-mail: <a href="mailto:dpo@uerj.br">dpo@uerj.br</a></p>
                <p>Endereço postal: Rua São Francisco Xavier, 524, Sala 1037 — Bloco A — Maracanã, Rio de Janeiro/RJ — CEP 20550-013</p>
                <p>Para denúncias, contate também a Autoridade Nacional de Proteção de Dados (ANPD): <a href="https://www.gov.br/anpd" target="_blank" rel="noopener">gov.br/anpd</a>.</p>
            </div>

            <h2 id="alteracoes">10. Alterações nesta política</h2>
            <p>Esta política pode ser atualizada para refletir mudanças legais, técnicas ou operacionais. A versão vigente é sempre a publicada nesta página, com data de atualização visível no índice.</p>
        </article>

    </div>
</section>
@endsection
