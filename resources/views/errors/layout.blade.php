@php
    $tenant = $tenant ?? (function () {
        try {
            return app(\App\Tenancy\TenantManager::class)->current()
                ?? \App\Models\Tenant::where('is_root', true)->first();
        } catch (\Throwable $e) {
            return null;
        }
    })();
    $accent = $tenant && !$tenant->is_root ? $tenant->accent_color : '#B92828';
    $accentDeep = $tenant && !$tenant->is_root ? $tenant->accent_deep_color : '#8E1B1B';
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Erro' }} · {{ $tenant?->short_name ?? 'PR-6' }} UERJ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --accent: {{ $accent }}; --accent-deep: {{ $accentDeep }}; }
        * { box-sizing: border-box; margin: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            display: flex; flex-direction: column;
            color: #1F2937;
            background: #FAF7F5;
            background-image:
                radial-gradient(1000px 600px at 80% -200px, rgba(245,145,150,.4), transparent 60%),
                radial-gradient(800px 500px at -10% 30%, rgba(245,145,150,.2), transparent 60%);
            background-attachment: fixed;
        }
        .err-bar { background: #0A102A; color: rgba(255,255,255,.8); font-size: 11px; }
        .err-bar div { max-width: 1200px; margin: 0 auto; padding: 8px 1.5rem; text-transform: uppercase; letter-spacing: .12em; font-weight: 600; }
        .err-main {
            flex: 1;
            display: flex; align-items: center; justify-content: center;
            padding: 3rem 1.5rem;
        }
        .err-card {
            background: #fff;
            border-radius: 28px;
            padding: clamp(2.5rem, 6vw, 4.5rem);
            max-width: 640px;
            width: 100%;
            text-align: center;
            box-shadow: 0 28px 60px rgba(185, 40, 40, .14);
            border: 1px solid #E7E2DE;
            border-top: 4px solid var(--accent);
        }
        .err-code {
            font-family: 'Manrope', sans-serif;
            font-size: clamp(5rem, 14vw, 9rem);
            font-weight: 800;
            color: transparent;
            background: linear-gradient(135deg, var(--accent), var(--accent-deep));
            -webkit-background-clip: text;
            background-clip: text;
            line-height: 1; letter-spacing: -.05em;
            margin-bottom: 1rem;
        }
        .err-title {
            font-family: 'Manrope', sans-serif;
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 800;
            letter-spacing: -.025em;
            margin-bottom: 1rem;
        }
        .err-message {
            color: #4B5563;
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            max-width: 480px;
            margin-left: auto; margin-right: auto;
        }
        .err-actions {
            display: flex; gap: .65rem; justify-content: center;
            flex-wrap: wrap;
        }
        .err-btn {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .85rem 1.5rem;
            border-radius: 999px;
            font-weight: 600; font-size: 14.5px;
            text-decoration: none;
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .err-btn--primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-deep));
            color: #fff;
            box-shadow: 0 8px 20px rgba(185, 40, 40, .28);
        }
        .err-btn--primary:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(185, 40, 40, .38); }
        .err-btn--secondary { background: #fff; color: #1F2937; border: 1px solid #E7E2DE; }
        .err-btn--secondary:hover { border-color: var(--accent); color: var(--accent); }
        .err-stripe {
            height: 4px;
            background: linear-gradient(90deg, var(--accent) 0%, #C9A35B 50%, var(--accent) 100%);
        }
        .err-foot {
            text-align: center;
            padding: 1.5rem;
            color: #6B7280;
            font-size: 12.5px;
        }
        .err-foot a { color: var(--accent); text-decoration: none; font-weight: 600; }
        .err-foot a:hover { text-decoration: underline; }
        .err-icon {
            display: inline-grid; place-items: center;
            width: 64px; height: 64px;
            background: rgba(185,40,40,.1);
            color: var(--accent);
            border-radius: 50%;
            margin-bottom: 1.5rem;
        }
        .err-icon svg { width: 32px; height: 32px; }
    </style>
</head>
<body>
    <div class="err-bar"><div>Universidade do Estado do Rio de Janeiro</div></div>
    <main class="err-main">
        <div class="err-card">
            @yield('icon')
            <div class="err-code">@yield('code', '404')</div>
            <h1 class="err-title">@yield('heading')</h1>
            <p class="err-message">@yield('message')</p>
            <div class="err-actions">
                <a href="/" class="err-btn err-btn--primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
                    Voltar à Home
                </a>
                <a href="/contato" class="err-btn err-btn--secondary">Fale conosco</a>
            </div>
        </div>
    </main>
    <div class="err-stripe"></div>
    <footer class="err-foot">
        © {{ date('Y') }} UERJ · {{ $tenant?->full_name ?? 'Pró-Reitoria de Planejamento e Gestão' }} ·
        <a href="/privacidade">Privacidade</a> · <a href="/lgpd">LGPD</a>
    </footer>
</body>
</html>
