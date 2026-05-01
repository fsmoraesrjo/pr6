<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Tenant;
use App\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AgendaController extends Controller
{
    private const TYPE_COLORS = [
        'reuniao' => '#1F2937',
        'evento' => '#B92828',
        'prazo' => '#B45309',
        'consulta' => '#7C3AED',
        'workshop' => '#0E7490',
    ];

    public function index(TenantManager $manager)
    {
        $tenants = $manager->isPortal()
            ? Tenant::where('is_active', true)->orderBy('order')->get()
            : collect([$manager->current()]);

        $types = [
            'reuniao' => 'Reunião',
            'evento' => 'Evento',
            'prazo' => 'Prazo',
            'consulta' => 'Consulta pública',
            'workshop' => 'Workshop',
        ];

        return view('site.agenda.index', compact('tenants', 'types'));
    }

    public function feed(Request $request, TenantManager $manager)
    {
        $query = $manager->isPortal()
            ? Event::query()->acrossTenants()->with('tenant')
            : Event::query();

        if ($tenantSlug = $request->query('d')) {
            $query->whereHas('tenant', fn ($q) => $q->where('slug', $tenantSlug));
        }

        if ($type = $request->query('tipo')) {
            $query->where('type', $type);
        }

        if ($start = $request->query('start')) {
            $query->where('starts_at', '>=', Carbon::parse($start)->subDays(1));
        }
        if ($end = $request->query('end')) {
            $query->where('starts_at', '<=', Carbon::parse($end)->addDays(1));
        }

        $events = $query->where('is_public', true)->orderBy('starts_at')->limit(500)->get();

        $payload = $events->map(function (Event $e) use ($manager) {
            $accent = $e->tenant?->accent_color ?? self::TYPE_COLORS[$e->type] ?? '#B92828';
            $tenantSlug = $e->tenant?->slug;

            return [
                'id' => $e->id,
                'title' => $e->title,
                'start' => $e->starts_at->toIso8601String(),
                'end' => $e->ends_at?->toIso8601String(),
                'allDay' => false,
                'backgroundColor' => $accent,
                'borderColor' => $accent,
                'textColor' => '#fff',
                'url' => null,
                'extendedProps' => [
                    'type' => $e->type,
                    'typeLabel' => match ($e->type) {
                        'reuniao' => 'Reunião',
                        'evento' => 'Evento',
                        'prazo' => 'Prazo',
                        'consulta' => 'Consulta pública',
                        'workshop' => 'Workshop',
                        default => ucfirst($e->type),
                    },
                    'tenant' => $e->tenant?->short_name,
                    'tenantSlug' => $tenantSlug,
                    'tenantColor' => $accent,
                    'location' => $e->location,
                    'isOnline' => (bool) $e->is_online,
                    'onlineUrl' => $e->online_url,
                    'description' => $e->description,
                    'icsUrl' => route('agenda.ics', $e->slug),
                    'startFormatted' => $e->starts_at->translatedFormat('d \d\e F \d\e Y · H\hi'),
                    'endFormatted' => $e->ends_at?->translatedFormat('d \d\e F \d\e Y · H\hi'),
                ],
            ];
        });

        return response()->json($payload);
    }

    public function ics(string $slug, TenantManager $manager)
    {
        $query = $manager->isPortal()
            ? Event::query()->acrossTenants()->with('tenant')
            : Event::query()->with('tenant');

        $event = $query->where('slug', $slug)->where('is_public', true)->firstOrFail();

        $start = $event->starts_at->setTimezone('UTC')->format('Ymd\THis\Z');
        $end = ($event->ends_at ?? $event->starts_at->copy()->addHour())->setTimezone('UTC')->format('Ymd\THis\Z');
        $now = now()->setTimezone('UTC')->format('Ymd\THis\Z');
        $uid = 'evento-' . $event->id . '@pr6.lumislabs.com.br';
        $location = $event->is_online && $event->online_url ? $event->online_url : ($event->location ?: 'A definir');
        $summary = $this->icsEscape($event->title);
        $description = $this->icsEscape(trim(($event->tenant?->short_name ? '[' . $event->tenant->short_name . '] ' : '') . ($event->description ?? '')));

        $ics = implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//PR-6 UERJ//Agenda//PT',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . $now,
            'DTSTART:' . $start,
            'DTEND:' . $end,
            'SUMMARY:' . $summary,
            'DESCRIPTION:' . $description,
            'LOCATION:' . $this->icsEscape($location),
            'STATUS:CONFIRMED',
            'END:VEVENT',
            'END:VCALENDAR',
        ]);

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $event->slug . '.ics"',
        ]);
    }

    private function icsEscape(string $value): string
    {
        return str_replace(["\\", ";", ",", "\n"], ["\\\\", "\\;", "\\,", "\\n"], $value);
    }
}
