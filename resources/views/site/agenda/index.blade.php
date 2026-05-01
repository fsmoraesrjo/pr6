@extends('layouts.site')

@section('title', 'Agenda · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Calendário institucional</span>
        <h1 class="page-head__title">Agenda{{ $tenant->is_root ? '' : ' da ' . $tenant->short_name }}</h1>
        <p class="page-head__lead">Reuniões, eventos, prazos e consultas públicas em um só lugar.</p>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="agenda-list">
    <div class="container">
        @if($events->isEmpty())
            <p class="empty-state">Nenhum evento agendado.</p>
        @else
            <ul class="agenda__list">
                @foreach($events as $e)
                    @php $accent = $e->tenant?->accent_color ?? $tenant->accent_color; @endphp
                    <li class="agenda-item" style="--accent:{{ $accent }}">
                        <div class="agenda-item__date">
                            <strong>{{ $e->starts_at->format('d') }}</strong>
                            <span>{{ \Illuminate\Support\Str::upper($e->starts_at->translatedFormat('M')) }}</span>
                        </div>
                        <div class="agenda-item__body">
                            <span class="agenda-item__type">{{ ucfirst($e->type) }}@if($e->tenant) · {{ $e->tenant->short_name }}@endif</span>
                            <h3>{{ $e->title }}</h3>
                            <p>
                                {{ $e->starts_at->format('d/m/Y H\hi') }}
                                @if($e->ends_at) até {{ $e->ends_at->format('H\hi') }} @endif
                                @if($e->location) · {{ $e->location }} @endif
                                @if($e->is_online && $e->online_url) · <a href="{{ $e->online_url }}">Acompanhar online</a> @endif
                            </p>
                            @if($e->description)<p>{{ $e->description }}</p>@endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
@endsection
