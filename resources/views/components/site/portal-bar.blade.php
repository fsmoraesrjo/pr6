@props(['current'])

@php
    $tenants = \App\Models\Tenant::query()->where('is_active', true)->orderBy('order')->get();
    $root = $tenants->firstWhere('is_root', true);
    $verticals = $tenants->where('is_root', false);
@endphp

<div class="portal-strip" role="navigation" aria-label="Diretorias da PR-6">
    <div class="container portal-strip__inner">
        <span class="portal-strip__label" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            Diretorias
        </span>

        <ul class="portal-strip__list" role="list">
            <li>
                <a href="{{ $root?->url() }}"
                   class="portal-tab portal-tab--root {{ $current?->is_root ? 'is-active' : '' }}"
                   style="--accent:#F59196;--accent-soft:#FCE4E5;--accent-deep:#B92828"
                   title="Portal PR-6"
                   aria-label="Portal PR-6 — Pró-Reitoria de Planejamento e Gestão">
                    <span class="portal-tab__dot" aria-hidden="true"></span>
                    <strong>PR-6</strong>
                </a>
            </li>
            @foreach($verticals as $v)
                <li>
                    <a href="{{ $v->url() }}"
                       class="portal-tab {{ $current?->id === $v->id ? 'is-active' : '' }}"
                       data-vertical="{{ $v->slug }}"
                       style="--accent:{{ $v->accent_color }};--accent-soft:{{ $v->accent_soft_color }};--accent-deep:{{ $v->accent_deep_color }}"
                       title="{{ $v->full_name }}"
                       aria-label="{{ $v->full_name }}">
                        <span class="portal-tab__dot" aria-hidden="true"></span>
                        <strong>{{ $v->short_name }}</strong>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
