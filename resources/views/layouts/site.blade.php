<!DOCTYPE html>
<html lang="pt-BR" data-theme="light" data-tenant="{{ $tenant?->slug ?? 'pr6' }}"
    @if($tenant && !$tenant->is_root)
        style="--accent:{{ $tenant->accent_color }};--accent-soft:{{ $tenant->accent_soft_color }};--accent-deep:{{ $tenant->accent_deep_color }}"
    @endif>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $pageTitle = trim((string) View::getSection('title')) ?: ($tenant?->short_name . ' UERJ');
        $pageDesc = $metaDescription ?? ($tenant?->description ?? 'Pró-Reitoria de Planejamento e Gestão da UERJ');
        $pageImage = $metaImage ?? asset('assets/hero-back.png');
        $pageUrl = url()->current();
        $siteName = $tenant?->short_name ? $tenant->short_name . ' UERJ' : 'PR-6 UERJ';
    @endphp
    <meta name="description" content="{{ $pageDesc }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ $pageUrl }}">
    <title>{{ $pageTitle }}</title>

    <meta property="og:type" content="website">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    <meta name="theme-color" content="{{ $tenant && !$tenant->is_root ? $tenant->accent_color : '#B92828' }}">
    <link rel="icon" href="{{ asset('assets/logo-pr6-cor.png') }}" type="image/png">

    {{-- DNS prefetch + preconnect para CDNs (acelera 1ª pintura) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    {{-- Preload da imagem do hero (LCP) --}}
    @if(request()->is('/'))
        @php
            $heroImage = match($tenant?->slug) {
                'dirtec' => 'assets/hero-dirtec.webp',
                'dirgis' => 'assets/hero-dirgis.webp',
                'dirplag' => 'assets/hero-dirplag.webp',
                'coomas' => 'assets/hero-coomas.webp',
                default => 'assets/hero-back.webp',
            };
        @endphp
        <link rel="preload" as="image" href="{{ asset($heroImage) }}" type="image/webp" fetchpriority="high">
    @endif

    {{-- Fonts (display=swap pra evitar FOIT) --}}
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=Atkinson+Hyperlegible:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/site.css') }}?v=16">
    <link rel="stylesheet" href="{{ asset('assets/site-pages.css') }}?v=6">
    <link rel="stylesheet" href="{{ asset('assets/site-agenda-people.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('assets/site-indicators.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('assets/site-content.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('assets/site-lgpd.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('assets/site-a11y.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('assets/site-mobile.css') }}?v=1">

    {{-- JSON-LD: Organization + WebSite (todas as páginas) --}}
    @php
        $orgJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'GovernmentOrganization',
            'name' => $tenant?->full_name ?? 'Pró-Reitoria de Planejamento e Gestão da UERJ',
            'alternateName' => $tenant?->short_name ?? 'PR-6',
            'url' => request()->getSchemeAndHttpHost(),
            'logo' => asset('assets/logo-pr6-cor.png'),
            'parentOrganization' => [
                '@type' => 'CollegeOrUniversity',
                'name' => 'Universidade do Estado do Rio de Janeiro',
                'url' => 'https://www.uerj.br',
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Rua São Francisco Xavier, 524',
                'addressLocality' => 'Rio de Janeiro',
                'addressRegion' => 'RJ',
                'postalCode' => '20550-013',
                'addressCountry' => 'BR',
            ],
            'sameAs' => ['https://www.uerj.br'],
        ];
        $siteJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => request()->getSchemeAndHttpHost(),
            'inLanguage' => 'pt-BR',
            'publisher' => ['@type' => 'GovernmentOrganization', 'name' => 'PR-6 UERJ'],
        ];
    @endphp
    <script type="application/ld+json">@json($orgJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
    <script type="application/ld+json">@json($siteJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>

    @stack('head')
</head>
<body @class(['vertical-theme' => $tenant && !$tenant->is_root])>

<a href="#conteudo" class="skip-link">Pular para o conteúdo principal</a>

<x-site.uerj-bar />
<x-site.header :tenant="$tenant" />

<main id="conteudo">
    {{ $slot ?? '' }}
    @yield('content')
</main>

<x-site.footer :tenant="$tenant" />
<x-site.lgpd-banner />

<script src="{{ asset('assets/site.js') }}?v=4" defer></script>
@stack('scripts')
</body>
</html>
