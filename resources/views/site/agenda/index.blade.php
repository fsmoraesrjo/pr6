@extends('layouts.site')

@section('title', 'Agenda · ' . $tenant->short_name)

@push('head')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
@endpush

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Agenda institucional</span>
        <h1 class="page-head__title">Agenda{{ $tenant->is_root ? '' : ' da ' . $tenant->short_name }}</h1>
        <p class="page-head__lead">Reuniões, eventos, prazos e consultas públicas em um só lugar. Filtre, abra os detalhes e exporte para o seu calendário.</p>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="agenda-page">
    <div class="container">

        <div class="agenda-page__filters" role="toolbar" aria-label="Filtros da agenda">
            @if($tenant->is_root && $tenants->count() > 1)
            <div class="agenda-filter-group">
                <span class="agenda-filter-group__label">Diretoria</span>
                <div class="agenda-chips" data-filter-group="tenant">
                    <button type="button" class="agenda-chip is-active" data-tenant="all">Todas</button>
                    @foreach($tenants as $t)
                        <button type="button"
                                class="agenda-chip"
                                data-tenant="{{ $t->slug }}"
                                style="--chip-accent:{{ $t->accent_color }}">{{ $t->short_name }}</button>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="agenda-filter-group">
                <span class="agenda-filter-group__label">Tipo</span>
                <div class="agenda-chips" data-filter-group="type">
                    <button type="button" class="agenda-chip is-active" data-type="all">Todos</button>
                    @foreach($types as $key => $label)
                        <button type="button"
                                class="agenda-chip"
                                data-type="{{ $key }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="agenda-calendar" class="agenda-calendar"></div>

    </div>
</section>

<div id="agenda-modal" class="agenda-modal" role="dialog" aria-modal="true" aria-labelledby="agenda-modal-title" hidden>
    <div class="agenda-modal__backdrop" data-close-modal></div>
    <div class="agenda-modal__panel" role="document">
        <button type="button" class="agenda-modal__close" data-close-modal aria-label="Fechar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
        <header class="agenda-modal__head" id="agenda-modal-head">
            <span class="agenda-modal__type"><span data-modal="typeLabel"></span> · <span data-modal="tenant"></span></span>
            <h2 id="agenda-modal-title" data-modal="title"></h2>
        </header>
        <dl class="agenda-modal__details">
            <div>
                <dt>Início</dt>
                <dd data-modal="startFormatted"></dd>
            </div>
            <div data-modal-when="endFormatted">
                <dt>Término</dt>
                <dd data-modal="endFormatted"></dd>
            </div>
            <div data-modal-when="location">
                <dt>Local</dt>
                <dd data-modal="location"></dd>
            </div>
            <div data-modal-when="isOnline">
                <dt>Transmissão</dt>
                <dd><a data-modal="onlineUrl" target="_blank" rel="noopener" data-modal-text="onlineUrl"></a></dd>
            </div>
        </dl>
        <p class="agenda-modal__description" data-modal="description" data-modal-when="description"></p>
        <footer class="agenda-modal__footer">
            <a href="#" class="btn btn--primary" data-modal-href="icsUrl" download>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15V3M5 12l7 7 7-7M5 21h14"/></svg>
                Adicionar ao meu calendário
            </a>
        </footer>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/pt-br.global.min.js"></script>
<script>
(function () {
    const el = document.getElementById('agenda-calendar');
    if (!el) return;
    const filters = { tenant: 'all', type: 'all' };
    const accents = {
        @foreach($tenants as $t)
            '{{ $t->slug }}': '{{ $t->accent_color }}',
        @endforeach
    };

    const calendar = new FullCalendar.Calendar(el, {
        locale: 'pt-br',
        initialView: 'dayGridMonth',
        firstDay: 0,
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana',
            list: 'Lista'
        },
        nowIndicator: true,
        dayMaxEventRows: 3,
        events: function (fetchInfo, success, failure) {
            const params = new URLSearchParams({
                start: fetchInfo.startStr,
                end: fetchInfo.endStr,
            });
            if (filters.tenant !== 'all') params.set('d', filters.tenant);
            if (filters.type !== 'all') params.set('tipo', filters.type);
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tenant')) params.set('tenant', urlParams.get('tenant'));
            fetch('/agenda/feed?' + params.toString(), { headers: { Accept: 'application/json' } })
                .then(r => r.json())
                .then(success)
                .catch(failure);
        },
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            openModal(info.event);
        },
        eventDidMount: function (info) {
            info.el.style.cursor = 'pointer';
        }
    });

    calendar.render();

    document.querySelectorAll('[data-filter-group]').forEach(group => {
        const key = group.dataset.filterGroup;
        group.querySelectorAll('.agenda-chip').forEach(btn => {
            btn.addEventListener('click', () => {
                group.querySelectorAll('.agenda-chip').forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');
                filters[key] = btn.dataset[key];
                calendar.refetchEvents();
            });
        });
    });

    function openModal(event) {
        const modal = document.getElementById('agenda-modal');
        const props = event.extendedProps;
        const data = {
            title: event.title,
            ...props,
        };

        modal.querySelectorAll('[data-modal]').forEach(node => {
            const key = node.dataset.modal;
            if (data[key] !== undefined && data[key] !== null && data[key] !== '') {
                if (node.tagName === 'A') {
                    node.href = data[key];
                    if (node.dataset.modalText) node.textContent = data[node.dataset.modalText];
                } else {
                    node.textContent = data[key];
                }
            } else if (node.tagName !== 'A') {
                node.textContent = '';
            }
        });
        modal.querySelectorAll('[data-modal-href]').forEach(node => {
            const key = node.dataset.modalHref;
            if (data[key]) node.setAttribute('href', data[key]);
        });
        modal.querySelectorAll('[data-modal-when]').forEach(node => {
            const key = node.dataset.modalWhen;
            node.style.display = data[key] ? '' : 'none';
        });

        const head = document.getElementById('agenda-modal-head');
        if (head) head.style.borderColor = data.tenantColor || '#B92828';

        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('agenda-modal');
        modal.hidden = true;
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-close-modal]').forEach(el => el.addEventListener('click', closeModal));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
})();
</script>
@endpush

@endsection
