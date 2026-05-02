# PR-6 UERJ — Status do Projeto

**Última atualização:** 2026-05-02
**Repo:** https://github.com/fsmoraesrjo/pr6
**Local:** `Z:\xampp\htdocs\pr6`

---

## 🌐 URLs vivas (homolog em produção)

### Portal mãe
- https://pr6.lumislabs.com.br
- https://pr6.lumislabs.com.br/admin/login (Filament)
- https://pr6.lumislabs.com.br/admin/profile (perfil + 2FA + passkeys)

### Verticais
- https://dirtec.pr6.lumislabs.com.br · DIRTEC (TI · azul)
- https://dirgis.pr6.lumislabs.com.br · DIRGIS (Infra/Serviços · ciano)
- https://dirplag.pr6.lumislabs.com.br · DIRPLAG (Planejamento · roxo)
- https://coomas.pr6.lumislabs.com.br · COOMAS (Sustentabilidade · verde)

### Páginas públicas (cada tenant tem todas)
| Rota | Conteúdo |
|---|---|
| `/` | Home (hero animado, blocos de notícias, agenda, indicadores, ouvidoria) |
| `/sobre` | Institucional (pilares, líder, CTAs) |
| `/noticias` | Lista paginada com busca + filtros |
| `/noticias/{slug}` | Detalhe com cover, breadcrumb, share, NewsArticle JSON-LD |
| `/documentos` | Repositório com filtros laterais por categoria |
| `/documentos/{slug}` | Detalhe com timeline de versões + downloads contabilizados |
| `/documentos/{slug}/download/{versionId?}` | Download direto |
| `/agenda` | FullCalendar (mês/semana/lista), filtros, modal, ICS export |
| `/agenda/feed` | JSON dos eventos |
| `/agenda/{slug}.ics` | Download iCalendar |
| `/indicadores` | Cards com sparkline ApexCharts ou progresso |
| `/indicadores/{slug}` | Detalhe com gráfico, tabela histórica, sidebar |
| `/pessoas` | Equipe agrupada por unidade organizacional |
| `/organograma` | Árvore hierárquica expansível |
| `/servicos` (verticais) | Catálogo com cards |
| `/servicos/{slug}` (verticais) | Detalhe com formulário inline |
| `/contato` | Fale conosco |
| `/buscar` | Busca global cross-content (notícias, documentos, serviços, pessoas) |
| `/privacidade` | Política LGPD com TOC |
| `/lgpd` | Formulário de direitos do titular (acesso/correção/exclusão/portabilidade/revogação/oposição) |
| `/sitemap.xml` | Sitemap dinâmico |
| `/sitemap-index.xml` | Index dos 5 sitemaps |
| `/robots.txt` | Dinâmico, aponta para sitemap |

### Páginas de erro customizadas
- 404, 403, 500, 503 com paleta PR-6 e logo

---

## 🔐 Credenciais

**Filament admin (HOMOLOG):**
- E-mail: `admin@pr6.uerj.br`
- Senha: `admin@pr6` ⚠️ **TROCAR ASSIM QUE LOGAR**

Em `/admin/profile` você pode:
- Trocar senha
- Ativar 2FA via TOTP (Google Authenticator/Authy/1Password)
- Cadastrar passkeys (WebAuthn — Touch ID, Windows Hello, YubiKey)

---

## 📦 Stack técnica

### Backend
- **PHP 8.3** + **Laravel 11** + **MariaDB 11** (MySQL-compatible)
- **Filament 3.2** admin
- **Livewire 3** (vai ser usado em forms futuros)
- **Spatie Permission** (RBAC), **Spatie Media Library**, **Spatie Activity Log**, **Spatie Laravel Passkeys**
- **stephenjude/filament-two-factor-authentication**
- **Scout + TNTSearch** (busca PHP-puro, sem daemon)

### Frontend
- **CSS puro** modular (7 arquivos: `site.css`, `site-pages.css`, `site-agenda-people.css`, `site-indicators.css`, `site-content.css`, `site-lgpd.css`, `site-a11y.css`, `site-mobile.css`)
- **Alpine.js + JS vanilla** (sem framework SPA)
- **FullCalendar 6** via CDN
- **ApexCharts** via CDN
- **Manrope + Inter + Atkinson Hyperlegible** (Google Fonts)

### Infra
- **VPS Hostinger** (Ubuntu 24.04, 8GB RAM, 2 vCPU)
- **Coolify** orquestrando
- **Traefik** reverse proxy + Let's Encrypt SSL
- **MariaDB** em container `pr6-db`
- **Aplicação** em container `pr6-portal` (Dockerfile próprio com PHP 8.3-apache)
- Volume persistente `pr6-storage` em `/var/www/html/storage`
- Auto-deploy desativado (deploy manual via script no Coolify)

---

## 🏗️ Arquitetura

### Multi-tenant single-database
- Tabela `tenants` com 5 registros: PR-6 (root) + DIRTEC + DIRGIS + DIRPLAG + COOMAS
- Trait `App\Tenancy\BelongsToTenant` com global scope automático
- Middleware `ResolveTenant` detecta tenant pelo `Host:` header
- Em dev usa `?tenant=slug` query param (não precisa mexer em `/etc/hosts`)
- Property `static $crossTenantOnPortal = true` em alguns models permite agregação na PR-6 mãe (notícias, eventos, documentos)

### Cores das diretorias
| Tenant | Accent | Soft | Deep |
|---|---|---|---|
| PR-6 | `#B92828` | `#FCE4E5` | `#8E1B1B` |
| DIRTEC | `#2563EB` | `#DBEAFE` | `#1E3A8A` |
| DIRGIS | `#0E7490` | `#CFFAFE` | `#155E75` |
| DIRPLAG | `#7C3AED` | `#EDE9FE` | `#5B21B6` |
| COOMAS | `#15803D` | `#DCFCE7` | `#14532D` |

Configuração de tema é via CSS custom properties no `<html>` por tenant.

---

## 📊 Banco — modelos principais

| Tabela | Conteúdo |
|---|---|
| `tenants` | 5 (PR-6 + 4 verticais) |
| `users` | 1 (admin) + suporte a 2FA + passkeys |
| `news`, `news_categories` | Notícias com FULLTEXT |
| `events` | Eventos da agenda |
| `documents`, `document_categories`, `document_versions` | Repositório versionado |
| `indicators`, `indicator_values` | Painel com source (manual/help_api/cic_api/sisuerj_api) |
| `services`, `service_categories` | Catálogo de serviços (verticais) |
| `org_units` | 15 unidades organizacionais (estrutura hierárquica) |
| `team_members` | 15 membros (titulares e equipe) |
| `form_submissions` | Histórico de envios (e-mail e CPF criptografados) |
| `consent_logs` | Aceite/recusa de cookies |
| `data_subject_requests` | Solicitações LGPD com prazo de 15 dias |
| `audit_logs` (`activity_log`) | Auditoria automática via Spatie |
| `media` | Anexos (Spatie Media Library) |
| `permission_*` | RBAC Spatie |
| `passkeys` | Spatie passkeys |

**Conteúdo demo plantado em prod:**
- 14 notícias (distribuídas entre os 5 tenants)
- 5 eventos
- 4 indicadores com séries históricas
- 3 documentos com versões
- 8 serviços (DIRTEC 3, DIRGIS 2, DIRPLAG 1, COOMAS 2)
- 15 pessoas + 15 unidades organizacionais

---

## ✅ Funcionalidades entregues

### Identidade visual
- Logos próprios PR-6 (cor + branca) no header e footer
- Hero com imagem `hero-back.webp` (115 KB) e overlay coral/vermelho
- Header padrão COPAD adaptado: imagem UERJ + overlay tingido + faixa dourada
- Strip de diretorias estilo Globo no topo (5 chips coloridos)
- Dark mode com toggle persistente

### Conteúdo
- Notícias: lista + detalhe com cover, share (WhatsApp, LinkedIn, e-mail, copiar link), NewsArticle JSON-LD
- Documentos: lista + detalhe com timeline de versões + RelationManager Filament para upload + download contabilizado
- Agenda: FullCalendar (mês/semana/lista) + filtros tenant/tipo + modal + ICS export
- Pessoas: cards agrupados por unidade
- Organograma: árvore expansível com cores accent
- Indicadores: cards com sparkline + detalhe com ApexCharts grande + tabela histórica + adapters HELP/CIC/SISUERJ preparados
- Serviços (verticais): catálogo + detalhe com formulário inline (request_type=internal_form/external_url/email/info_only)
- Sobre PR-6: pilares, líder, CTA institucional
- Notícias detalhe com structured data NewsArticle + BreadcrumbList

### Busca + SEO
- Busca global `/buscar` cross-content (notícias, documentos, serviços, pessoas) com tabs e highlight `<mark>`
- Sitemap dinâmico por tenant + sitemap index multi-tenant
- robots.txt dinâmico
- Open Graph + Twitter Cards em todas as páginas
- Canonical URL
- theme-color por tenant
- JSON-LD GovernmentOrganization + WebSite + NewsArticle + BreadcrumbList

### Compliance LGPD
- Banner de cookies com 3 níveis (Essenciais/Analíticos/Marketing)
- Endpoint `/lgpd/consent` registra em `consent_logs` (session/IP/UA em hash SHA-256)
- Política de privacidade `/privacidade` com 10 seções e TOC
- Formulário `/lgpd` com 6 tipos de solicitação (LGPD Art. 18)
- Validação de CPF (dígitos verificadores) + máscara JS
- Criptografia AES-256 de e-mail e CPF em repouso
- Prazo legal de 15 dias automático
- Número de protocolo no recibo
- E-mails de notificação enviados para `pr6@uerj.br` (configurável via `PR6_CONTACT_EMAIL`)
- DPO `dpo@uerj.br` no rodapé

### Acessibilidade WCAG/eMAG AA
- Barra UERJ com 4 botões: A+, A-, alto contraste, painel completo (♿)
- Painel modal com 6 opções (incluindo fonte legível Atkinson Hyperlegible)
- Persistência em localStorage
- Modo alto contraste paleta preto/amarelo WCAG AAA
- Skip link "pular para o conteúdo"
- prefers-reduced-motion respeitado
- Touch targets ≥ 44px no mobile
- ARIA labels em todos os botões interativos

### Segurança
- 2FA TOTP + Passkeys (WebAuthn) no Filament admin
- Audit log automático em 7 models críticos via Spatie ActivityLog
- Headers HTTP de segurança (X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy)
- Cache-Control 1 ano em assets versionados
- HTTPS forçado (Traefik + Let's Encrypt)
- Trust proxies configurado pra Traefik
- IPs em hash SHA-256 (LGPD)
- Senhas em bcrypt (12 rounds)
- Session encrypt = true em produção, cookie SameSite

### Mobile
- Hamburger funcional (animação 3 traços → X)
- Drawer lateral 360px com slide animation
- Menu completo + busca inline + 4 chips das diretorias + acesso restrito
- Touch targets ≥ 44px
- Esc fecha + click no backdrop fecha

### Performance
- WebP no hero (115 KB vs 2118 KB do PNG, 94% redução)
- Logos em WebP (50% menor)
- Cache-Control `public, max-age=31536000, immutable` em assets
- Compressão DEFLATE em CSS/JS/JSON/XML/SVG
- `<link rel="preload">` + `fetchpriority="high"` no hero
- `<link rel="dns-prefetch">` para CDNs externos
- `defer` no JS principal
- `loading="lazy"` + `decoding="async"` em imagens
- `width`/`height` explícitos (evita CLS)
- `display=swap` nas fontes (evita FOIT)

### Páginas de erro
- 404, 403, 500, 503 customizadas com paleta PR-6 e logo

---

## 📧 Configuração de e-mail

| Variável | Valor atual | Observação |
|---|---|---|
| `MAIL_MAILER` | `log` | ⚠️ E-mails ficam no log, não saem |
| `PR6_CONTACT_EMAIL` | `pr6@uerj.br` | Destino de TODOS os formulários |

**Formulários que enviam e-mail:**
- Solicitação de serviço (`/servicos/{slug}`) → `pr6@uerj.br`
- Direitos do titular LGPD (`/lgpd`) → `pr6@uerj.br`

⚠️ **ANTES DE DIVULGAR O PORTAL**: configurar SMTP real no Coolify trocando `MAIL_MAILER=log` por SMTP institucional UERJ ou Hostinger.

---

## ⏭️ O que falta — agrupado por bloqueador

### ⚪ Operacional (você executa)
1. ⚠️ **Trocar senha admin** + ativar 2FA do seu user em `/admin/profile`
2. ⚠️ **Configurar SMTP real** no Coolify (trocar `MAIL_MAILER=log` por SMTP)
3. **Substituir conteúdo demo** pelos reais via Filament admin
4. **Logos próprios das 4 diretorias** (envio pendente)
5. **Textos institucionais reais** da PR-6

### 🔌 Aguardando outros
- HELP API expor `/api/stats` para alimentar indicadores de chamados
- CIC publicar página de resumo de contratos para iframe
- Equipe de redes UERJ liberar wildcard `*.pr6.uerj.br`

### 🟢 Posso entregar (técnico, próxima sprint)
- Página individual de evento `/agenda/{slug}` com JSON-LD `Event`
- Filtros nas notícias (categoria + diretoria + paginação)
- Honeypot + rate limiting nos forms (anti-spam/bot)
- RSS feed `/noticias.rss`
- PWA básica (manifest + service worker, instalável no celular)
- Newsletter com double opt-in
- Galeria de fotos
- Multi-idioma PT/EN/ES
- Lighthouse audit completo + ajustes finos

---

## 🛠️ Comandos úteis

### Local (XAMPP)
```bash
# Subir o servidor de dev
cd Z:/xampp/htdocs/pr6 && Z:/xampp/php/php.exe artisan serve --host=127.0.0.1 --port=5183

# Acessar com tenant em dev
http://127.0.0.1:5183/?tenant=pr6
http://127.0.0.1:5183/?tenant=dirtec

# Migration / seed
Z:/xampp/php/php.exe artisan migrate
Z:/xampp/php/php.exe artisan db:seed
Z:/xampp/php/php.exe artisan db:seed --class=DemoTeamSeeder
```

### Produção (via SSH no hub-vps)
```bash
# Logs do container PR-6
ssh hub-vps "sudo docker logs --tail 50 \$(sudo docker ps --format '{{.Names}}' | grep mbbludqjhegvc5tn2uipa2q4)"

# Tinker em produção
ssh hub-vps "sudo docker exec -it <container> php artisan tinker"

# Trigger deploy manual
ssh hub-vps 'sudo docker exec coolify php /tmp/pr6-deploy.php'

# Ver status do último deploy
ssh hub-vps 'sudo docker exec coolify php /tmp/dep-status.php'
```

### Variáveis de ambiente em produção (Coolify)
```
APP_NAME=PR-6 UERJ
APP_ENV=production
APP_KEY=base64:v1AfFhyfIZyAddbpLFJqvwnTlkEVQxCzoacAWCa7O48=
APP_URL=https://pr6.lumislabs.com.br
DB_HOST=eyp3bct8vdrhg3obmi919419
DB_DATABASE=pr6
DB_USERNAME=pr6
DB_PASSWORD=Y71uraHHzeEicnyS8XDwb2Oif29OTuaF
SESSION_DOMAIN=.lumislabs.com.br
PR6_ROOT_DOMAIN=pr6.lumislabs.com.br
PR6_CONTACT_EMAIL=pr6@uerj.br
DB_SEED_ON_BOOT=false
```

---

## 📝 Decisões importantes registradas

1. **Multi-tenancy single-database** com `tenant_id` + global scope (não usamos stancl/tenancy)
2. **TNTSearch substitui Meilisearch** (ambiente UERJ futuro sem daemon)
3. **Vite só para Filament**; site público usa CSS estático modular
4. **Logo PR-6 oficial** (cor + branca) usado conforme manual da PR-6
5. **Hero com imagem de fundo + overlay vermelho/coral** (preserva legibilidade)
6. **Strip de diretorias estilo Globo no topo** (decidiu trocar do bloco gigante no meio da home)
7. **WebP para imagens** mantendo PNG só para og:image e favicon (compatibilidade crawlers)
8. **Formulários enviam para `pr6@uerj.br`** — sem painel de gestão (decisão do PR)
9. **Auto-deploy desligado** — deploy é manual via comando
10. **DB_SEED_ON_BOOT=false** depois da primeira subida — seeders não rodam novamente

---

## 🔒 Backup recomendado

**A fazer antes de sair em divulgação:**
1. Backup do MySQL via Coolify > Database > Backup
2. Backup do volume `pr6-storage` (uploads de documentos, fotos)
3. Snapshot do repo GitHub na branch `main`

---

## 📞 Contatos do projeto

- **Diretor de TI da UERJ:** Fábio Moraes (fsmoraes@gmail.com)
- **DPO institucional:** dpo@uerj.br
- **Repo:** https://github.com/fsmoraesrjo/pr6
- **Coolify dashboard:** https://coolify.lumislabs.com.br/project/n10yuln2n46j4oqaije56lop/environment/fjx1rn368llrddvjojlop2ts/application/mbbludqjhegvc5tn2uipa2q4

---

**Status final desta sprint:** Bloco 1 (Transparência), Bloco 2 (Conteúdo), Bloco 3 (LGPD), Bloco 4 (Segurança/A11y/SEO/Erros) e Bloco Mobile/Busca/E-mails completos. Aguardando conteúdo real, SMTP e DNS UERJ para divulgação oficial.
