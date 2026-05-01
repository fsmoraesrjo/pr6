# PR-6 UERJ · Handoff Sprint 1 → Sprint 2

**Data:** 2026-05-01
**Localização do projeto:** `Z:\xampp\htdocs\pr6`
**Mockup de referência:** `Z:\xampp\htdocs\pr6-mockup` (HTML estático)
**Servidor de dev rodando em:** http://127.0.0.1:5183 (`Z:/xampp/php/php.exe artisan serve --host=127.0.0.1 --port=5183`)
**Banco:** MySQL `pr6_dev` no XAMPP local (PHP 8.2.12 + MariaDB 10.4)

---

## Briefing consolidado

Portal institucional multi-tenant da Pró-Reitoria de Planejamento e Gestão (PR-6) da UERJ. Modelo Globo.com: portal mãe + 4 verticais editoriais com identidade própria.

**Verticais:**
| Slug | Nome completo | Accent |
|---|---|---|
| pr6 (root) | Pró-Reitoria de Planejamento e Gestão | #B92828 vermelho |
| dirtec | Diretoria de Tecnologia da Informação e Comunicação | #2563EB azul |
| dirgis | Diretoria de Gestão da Infraestrutura e Serviços | #0E7490 ciano |
| dirplag | Diretoria de Planejamento em Infraestrutura | #7C3AED roxo |
| coomas | Coordenação de Meio Ambiente e Sustentabilidade | #15803D verde |

**Domínios dev (atuais):** `pr6.lumislabs.com.br` + `<slug>.pr6.lumislabs.com.br`
**Domínios prod (futuro, ainda sem acesso ao DNS uerj.br):** `pr6.uerj.br` + `<slug>.pr6.uerj.br`

**Restrição crítica de produção UERJ:** ambiente sem shell. Vendor versionado, assets pré-compilados, deploy via `public/fix-*.php` (mesmo padrão TUCARUANDA). Por isso:
- Sem Meilisearch (foi substituído por TNTSearch PHP puro)
- Sem npm em produção (CSS site público é estático em `public/assets/`, não usa Vite)
- Vite continua para o Filament admin (já compilado pelo composer require)

**Identidade visual:** Manrope (títulos) + Inter (corpo). Tom institucional moderno com ousadia máxima, dark mode com toggle. Logos próprios das 4 diretorias (a receber até final da Sprint 1).

**Compliance:** LGPD kit completo (banner cookies, DPO, formulário de direitos do titular com prazo de 15 dias, criptografia de CPF). WCAG/eMAG nível AA com barra de acessibilidade.

---

## Stack

```
PHP 8.3 (prod) / 8.2.12 (dev) · Apache · MySQL/MariaDB
Laravel 11 + Filament 3 + Livewire 3 + Alpine.js
Spatie Permission + Spatie Media Library
Scout + TNTSearch (busca PHP puro)
ApexCharts (próximo) + FullCalendar (próximo)
```

---

## O que está pronto (Sprint 1 ✅)

### Estrutura de pastas adicionadas

```
app/
├── Tenancy/
│   ├── TenantManager.php          (singleton, resolve por host ou ?tenant=slug em dev)
│   └── BelongsToTenant.php        (trait com global scope + auto-fill tenant_id)
├── Http/
│   ├── Middleware/ResolveTenant.php
│   └── Controllers/Site/HomeController.php
└── Models/
    ├── Tenant.php
    ├── News.php + NewsCategory.php
    ├── Event.php
    ├── Document.php + DocumentCategory.php + DocumentVersion.php
    ├── OrgUnit.php
    ├── TeamMember.php
    ├── Indicator.php + IndicatorValue.php
    └── Service.php + ServiceCategory.php

config/pr6.php                     (root_domain, root_domain_prod, perfis das tenants)

database/
├── migrations/2026_05_01_120000..120080  (14 migrations)
└── seeders/
    ├── DatabaseSeeder.php          (cria admin@pr6.uerj.br + roda os outros)
    ├── TenantSeeder.php            (5 tenants)
    └── DemoContentSeeder.php       (notícias, eventos, indicadores demo)

resources/views/
├── layouts/site.blade.php
├── components/site/
│   ├── uerj-bar.blade.php
│   ├── portal-bar.blade.php       (navegação cruzada PR-6 ↔ verticais)
│   ├── header.blade.php
│   ├── footer.blade.php
│   ├── lgpd-banner.blade.php
│   └── tenant-icon.blade.php
└── site/
    ├── portal/home.blade.php       (home do PR-6 mãe, agrega cross-tenant)
    ├── vertical/home.blade.php     (home de cada diretoria, accent dinâmico)
    └── placeholder.blade.php

public/assets/
├── site.css                        (CSS base + portal-bar + vertical-theme)
└── site.js                         (theme toggle, count-up, scroll reveal, filter, lgpd)
```

### Multi-tenancy

- `App\Tenancy\TenantManager`: singleton injetado no container. Métodos `current()`, `id()`, `isPortal()`, `isVertical()`, `resolveByHost($host)`
- `App\Tenancy\BelongsToTenant`: trait nos models. Adiciona global scope `tenant` que filtra automaticamente por `tenant_id`. Para agregação cross-tenant na home do portal, model define `public static bool $crossTenantOnPortal = true` (caso de News, Event, Document) — quando o tenant atual é root, scope se desativa
- Scopes auxiliares: `forTenant($tenant)` força um tenant específico, `acrossTenants()` desativa o scope para queries explícitas
- Middleware `ResolveTenant` no pipeline web: lê `Host`, fallback para `?tenant=slug` em local
- View shares `$tenant` e `$isPortal` em todos os templates

### Banco

Todas as 14 migrations executadas. Schema completo:
- `tenants`, `tenant_user` (pivot multi-papel)
- `org_units`, `team_members`
- `news_categories`, `news` (com FULLTEXT)
- `document_categories`, `documents` (com FULLTEXT), `document_versions`
- `events`
- `indicators`, `indicator_values`
- `service_categories`, `services`
- `form_submissions` (CPF e e-mail criptografados, IP hash)
- `consent_logs`
- `data_subject_requests` (deadline_at indexado)
- `audit_logs`
- Extensão de `users`: two_factor_secret, two_factor_enabled, last_login_at, avatar_path, is_active

### Frontend público

- Layout Blade com tema dinâmico via CSS custom properties por tenant (`--accent`, `--accent-soft`, `--accent-deep` no `<html>`)
- Componentes reusáveis com vínculo automático ao tenant atual
- Home da PR-6 mãe: hero institucional + bloco das 4 diretorias com hover que tinge a página + agregação cross-tenant
- Home das verticais: hero centralizado com tagline própria + conteúdo escopado da diretoria
- CSS único em `public/assets/site.css` (sem build), tema light + dark mode + variants `vertical-theme`

### Seeds

5 tenants com cores e taglines, 14 notícias distribuídas, 5 eventos, 4 indicadores com séries históricas mensais.

---

## O que falta (Sprint 2 a partir daqui)

**Prioridade 1 · Filament admin**
1. Criar usuário admin (já tem `admin@pr6.uerj.br` mas é preciso senha conhecida e gerar painel)
2. `php artisan make:filament-user`
3. Resources Filament tenant-aware: NewsResource, EventResource, DocumentResource (com VersionsRelationManager), ServiceResource, TeamMemberResource, OrgUnitResource, IndicatorResource (+ ValuesRelationManager), TenantResource, UserResource
4. Seletor de tenant ativo no header do painel (super-admin) + escopo automático para usuários multi-tenant
5. Scope de Filament aplicar `BelongsToTenant` corretamente

**Prioridade 2 · Páginas internas**
6. Lista de notícias com paginação e busca (`/noticias`)
7. Detalhe de notícia (`/noticias/{slug}`)
8. Lista de documentos com filtros laterais por categoria, busca TNTSearch, badge de versão, modal de preview (`/documentos`)
9. Detalhe de documento com timeline de versões e download (`/documentos/{slug}`)
10. Agenda com FullCalendar (`/agenda`)
11. Catálogo de serviços (`/servicos`) — só nas verticais — com formulário de solicitação
12. Lista e detalhe de indicadores com gráficos ApexCharts (`/indicadores`, `/indicadores/{slug}`)
13. Equipe (`/equipe`) e organograma SVG interativo (`/organograma`)
14. Sobre (`/sobre`) com texto institucional

**Prioridade 3 · LGPD + a11y + área restrita**
15. Banner LGPD com 3 níveis (essencial, analítico, marketing) + log em `consent_logs`
16. Formulário de direitos do titular (Lei 13.709/2018) com criptografia de CPF e e-mail, prazo de 15 dias automático
17. Login próprio bcrypt + 2FA opcional (Laravel Fortify ou Filament's auth)
18. Audit log de toda ação no admin
19. Barra de acessibilidade fixa (lateral): A+, A-, alto contraste, sublinhar links, fonte legível
20. Sitemap XML + robots.txt + meta tags Open Graph

**Prioridade 4 · Polimento e deploy UERJ**
21. Animações scroll-triggered no front (já tem o JS base)
22. Lighthouse 95+ em todas as páginas
23. Documento `docs/DEPLOY-UERJ.md` com procedimento sem shell
24. Vendor versionado, build pre-compilado, fix-scripts iniciais
25. Configuração Apache wildcard (`ServerAlias *.pr6.lumislabs.com.br`)

---

## Comandos úteis

```bash
# Subir o servidor de dev
cd Z:/xampp/htdocs/pr6 && Z:/xampp/php/php.exe artisan serve --host=127.0.0.1 --port=5183

# Resetar banco e reseedar
Z:/xampp/php/php.exe artisan migrate:fresh --seed

# Rodar artisan qualquer comando (via PHP do XAMPP)
Z:/xampp/php/php.exe artisan <comando>

# Composer (sempre via Z:/xampp/php para garantir 8.2)
Z:/xampp/php/php.exe C:/ProgramData/ComposerSetup/bin/composer.phar <comando>

# Ver tenants
Z:/xampp/php/php.exe artisan tinker
> App\Models\Tenant::all(['slug','short_name','accent_color']);
```

## URLs de validação local

```
Portal PR-6  http://127.0.0.1:5183/?tenant=pr6
DIRTEC       http://127.0.0.1:5183/?tenant=dirtec
DIRGIS       http://127.0.0.1:5183/?tenant=dirgis
DIRPLAG      http://127.0.0.1:5183/?tenant=dirplag
COOMAS       http://127.0.0.1:5183/?tenant=coomas
```

---

## Decisões importantes registradas

- Multi-tenancy single-database com `tenant_id` global scope (não usamos stancl/tenancy)
- TNTSearch substitui Meilisearch (ambiente UERJ sem daemon)
- Vite só para Filament; site público usa CSS estático
- Em dev usa-se `?tenant=slug` no lugar de subdomínio (sem mexer no /etc/hosts); resolver por Host só ativa em ambiente real
- Logos das 4 diretorias ainda a receber (Fábio enviará até final da Sprint 1)
- Wildcard DNS `*.pr6.uerj.br` ainda não disponível; mantemos só `*.pr6.lumislabs.com.br` em dev/homolog até que a UERJ libere

---

## Para reabrir o projeto em nova janela

1. Avise: "vamos continuar a Sprint 2 da PR-6, ler `Z:\xampp\htdocs\pr6\HANDOFF-SPRINT-1.md`"
2. Confirmar que o servidor está parado (ou reiniciar com o comando acima)
3. Começar pelo Filament admin (prioridade 1)
