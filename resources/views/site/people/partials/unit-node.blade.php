@php
    $children = $byParent->get($unit->id, collect());
    $accent = $unit->tenant?->accent_color ?? '#B92828';
    $head = $unit->members?->firstWhere('is_head', true) ?? $unit->members?->first();
    $depth = $depth ?? 0;
@endphp

<div class="org-node {{ $depth === 0 ? 'org-node--root is-open' : '' }}"
     data-unit-id="{{ $unit->id }}"
     style="--accent:{{ $accent }}"
     role="treeitem"
     aria-expanded="{{ $depth === 0 ? 'true' : 'false' }}">
    <button type="button" class="org-node__head" aria-label="{{ $unit->name }}">
        @if($unit->tenant && $unit->tenant->is_root === false)
            <span class="org-node__tenant">{{ $unit->tenant->short_name }}</span>
        @endif
        <span class="org-node__type">{{ ucfirst($unit->type) }}</span>
        <strong class="org-node__name">{{ $unit->name }}</strong>
        @if($head)
            <span class="org-node__head-person">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                {{ $head->name }}
            </span>
        @endif
        @if($children->count())
            <span class="org-node__toggle" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
            </span>
        @endif
    </button>

    @if($unit->members && $unit->members->count() > 0)
        <div class="org-node__members">
            @foreach($unit->members->take(8) as $m)
                <div class="org-mini">
                    @if($m->photo_path)
                        <img src="{{ asset('storage/' . $m->photo_path) }}" alt="{{ $m->name }}" loading="lazy">
                    @else
                        <span aria-hidden="true">{{ collect(explode(' ', $m->name))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}</span>
                    @endif
                    <div>
                        <strong>{{ $m->name }}</strong>
                        <small>{{ $m->role_title }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($children->count())
        <div class="org-node__children" role="group">
            @foreach($children as $child)
                @include('site.people.partials.unit-node', ['unit' => $child, 'byParent' => $byParent, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
