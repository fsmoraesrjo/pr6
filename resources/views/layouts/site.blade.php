<!DOCTYPE html>
<html lang="pt-BR" data-theme="light"
    @if($tenant && !$tenant->is_root)
        style="--accent:{{ $tenant->accent_color }};--accent-soft:{{ $tenant->accent_soft_color }};--accent-deep:{{ $tenant->accent_deep_color }}"
    @endif>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? ($tenant?->description ?? config('app.name')) }}">
    <title>@yield('title', $tenant?->short_name . ' UERJ')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/site.css') }}?v=12">
    <link rel="stylesheet" href="{{ asset('assets/site-pages.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('assets/site-agenda-people.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('assets/site-indicators.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('assets/site-content.css') }}?v=1">
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

<script src="{{ asset('assets/site.js') }}?v=2"></script>
@stack('scripts')
</body>
</html>
