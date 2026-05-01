# PR-6 · Deploy Hostinger VPS via Coolify

Domínios alvo:
- **pr6.lumislabs.com.br** (portal mãe)
- **dirtec.pr6.lumislabs.com.br**
- **dirgis.pr6.lumislabs.com.br**
- **dirplag.pr6.lumislabs.com.br**
- **coomas.pr6.lumislabs.com.br**

Stack: PHP 8.3 + Apache + MySQL via container Coolify, Traefik faz HTTPS.

---

## Etapa 1 · DNS no painel da Hostinger

No DNS de **lumislabs.com.br**, criar 5 registros tipo A apontando para o IP da VPS Hostinger (mesmo IP onde rodam VITHA, SISUERJ):

| Nome | Tipo | Valor |
|---|---|---|
| `pr6` | A | IP_DA_VPS |
| `dirtec.pr6` | A | IP_DA_VPS |
| `dirgis.pr6` | A | IP_DA_VPS |
| `dirplag.pr6` | A | IP_DA_VPS |
| `coomas.pr6` | A | IP_DA_VPS |

Atalho: pode usar **um registro wildcard** `*.pr6` apontando para o mesmo IP (resolve todos os subdomínios de uma vez), e **outro registro `pr6` direto** apontando para o IP. Esse é o caminho recomendado.

> Propagação DNS: 5 min a 1 hora dependendo do TTL.

---

## Etapa 2 · Repositório Git

Aqui no XAMPP (já vou deixar pronto):

```bash
cd Z:/xampp/htdocs/pr6
git init -b main
git add .
git commit -m "PR-6 portal multi-tenant inicial · pronto pra Coolify"
```

Depois cria repo privado em `fsmoraesrjo/pr6` no GitHub e:

```bash
git remote add origin git@github.com:fsmoraesrjo/pr6.git
git push -u origin main
```

---

## Etapa 3 · Banco de dados no Coolify

1. Acessa Coolify → **Servers** → seu hub-vps → **+ New Resource** → **Database** → **MySQL** (ou MariaDB)
2. Nome: `pr6-mysql`
3. Usa imagem `mysql:8` ou `mariadb:11`
4. Cria com:
   - Database name: `pr6`
   - Username: `pr6`
   - Password: gere uma forte
5. Anota o **Internal Hostname** (algo como `pr6-mysql` na rede interna do Coolify) — vai pro `DB_HOST` do app.

---

## Etapa 4 · Aplicação no Coolify

1. **+ New Resource** → **Application** → **Public Repository** (ou Private com PAT)
2. URL do repo: `git@github.com:fsmoraesrjo/pr6.git` (branch `main`)
3. **Build Pack**: `Dockerfile` (Coolify detecta automaticamente)
4. **Port exposed**: `80`
5. **Domain**:
   ```
   https://pr6.lumislabs.com.br,
   https://dirtec.pr6.lumislabs.com.br,
   https://dirgis.pr6.lumislabs.com.br,
   https://dirplag.pr6.lumislabs.com.br,
   https://coomas.pr6.lumislabs.com.br
   ```
   (todos no mesmo app — Coolify configura Traefik com SSL Let's Encrypt automático)

---

## Etapa 5 · Variáveis de ambiente

Em **Application → Environment Variables**, cole o conteúdo de `.env.production.template`. Preencha em especial:

- `APP_KEY` — gere localmente com `php artisan key:generate --show` e cole o valor
- `DB_HOST` — nome interno do banco (ex.: `pr6-mysql`)
- `DB_PASSWORD` — a senha que você definiu no Coolify
- `MAIL_PASSWORD` — senha do SMTP Hostinger (se já tiver e-mail)
- `DB_SEED_ON_BOOT=true` apenas na **primeira subida** (depois mude para `false` ou remova)

---

## Etapa 6 · Persistência de uploads

Em **Application → Storages**, criar volume:

- **Mount path**: `/var/www/html/storage`
- **Name**: `pr6-storage`

Isso garante que uploads de documentos, fotos de equipe, capas de notícias etc não somem em redeploy.

---

## Etapa 7 · Deploy

1. Click **Deploy** no Coolify
2. Acompanhe o build (build → push imagem → start container)
3. Na primeira subida o entrypoint roda:
   - `php artisan storage:link`
   - `php artisan migrate --force`
   - `php artisan db:seed --force` (se `DB_SEED_ON_BOOT=true`)
   - `php artisan config:cache && route:cache && view:cache`
4. Apache sobe e Traefik conecta com SSL

Depois de validar, **mude `DB_SEED_ON_BOOT` para `false`** e redeploy (o seeder só deve rodar 1 vez).

---

## Etapa 8 · Validação

```
https://pr6.lumislabs.com.br                  → portal mãe
https://dirtec.pr6.lumislabs.com.br           → DIRTEC
https://dirgis.pr6.lumislabs.com.br           → DIRGIS
https://dirplag.pr6.lumislabs.com.br          → DIRPLAG
https://coomas.pr6.lumislabs.com.br           → COOMAS
https://pr6.lumislabs.com.br/admin/login      → Filament admin
```

Login admin inicial:
- E-mail: `admin@pr6.uerj.br`
- Senha: `admin@pr6` (TROCAR ASSIM QUE LOGAR — Filament > meu perfil)

---

## Comandos úteis dentro do container

Coolify tem um terminal web por aplicação. Comandos comuns:

```bash
# Rodar artisan
php artisan tinker

# Limpar cache
php artisan config:clear && php artisan route:clear && php artisan view:clear

# Re-seed parcial
php artisan db:seed --class=DemoDocumentsSeeder --force

# Criar novo admin
php artisan tinker
> $u = App\Models\User::create(['name'=>'Fulano','email'=>'fulano@uerj.br','password'=>Hash::make('senha-forte'),'is_active'=>true]);
```

---

## Troubleshooting

**Erro 500 no primeiro acesso**
- Verificar `APP_KEY` preenchida
- Verificar `DB_HOST` e `DB_PASSWORD` corretos
- Olhar logs em Coolify > Application > Logs

**Subdomínio não resolve**
- DNS ainda não propagou (`dig dirtec.pr6.lumislabs.com.br`)
- Coolify não tem aquele domínio no campo Domain
- Verificar regra wildcard `*.pr6` no DNS

**SSL não emite**
- Aguardar propagação total do DNS
- Forçar redeploy
- Ver logs do Traefik no Coolify

**Sessão não persiste entre subdomínios**
- Confirmar `SESSION_DOMAIN=.lumislabs.com.br` no env
- `SESSION_DRIVER=database` (não `file`, que isola por container)

**Login admin redireciona em loop**
- `APP_URL` precisa bater com o domínio acessado
- `TRUSTED_PROXIES=*` setado
- O `TrustProxies` no `bootstrap/app.php` já está configurado pra honrar o `X-Forwarded-Proto` do Traefik

---

## Migração futura para uerj.br

Quando o DNS `*.pr6.uerj.br` for liberado pela equipe de redes UERJ:

1. Adicionar os 5 domínios `*.pr6.uerj.br` no Coolify (campo Domain do app)
2. Atualizar `APP_URL=https://pr6.uerj.br`
3. No banco, atualizar `tenants.domain_prod`:
   ```sql
   -- já está seedado corretamente, conferir
   SELECT slug, domain_dev, domain_prod FROM tenants;
   ```
4. Ajustar `SESSION_DOMAIN=.uerj.br` (ou manter `.lumislabs.com.br` durante migração paralela)
5. Redeploy

O código já está preparado: `Tenant::url()` distingue dev/prod via `app()->environment()`.
