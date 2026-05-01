@extends('layouts.site')

@section('title', 'Organograma · ' . $tenant->short_name)

@section('content')
<section class="page-head">
    <div class="container">
        <span class="section-head__eyebrow">{{ $tenant->short_name }} · Estrutura</span>
        <h1 class="page-head__title">Organograma</h1>
        <p class="page-head__lead">Visualize a estrutura organizacional da {{ $tenant->is_root ? 'PR-6' : $tenant->short_name }}. Clique em uma unidade para ver os profissionais responsáveis.</p>
        <div class="page-head__cta">
            <a href="/pessoas" class="btn btn--gold">
                Ver lista de pessoas
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
    <div class="page-head__stripe" aria-hidden="true"></div>
</section>

<section class="chart-page">
    <div class="container">
        @if($roots->isEmpty())
            <p class="empty-state">Estrutura organizacional em cadastramento.</p>
        @else
            <div class="org-chart" role="tree">
                @foreach($roots as $root)
                    @include('site.people.partials.unit-node', ['unit' => $root, 'byParent' => $byParent, 'depth' => 0])
                @endforeach
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.querySelectorAll('.org-node__head').forEach(head => {
    head.addEventListener('click', () => {
        const node = head.closest('.org-node');
        node.classList.toggle('is-open');
    });
});
</script>
@endpush

@endsection
