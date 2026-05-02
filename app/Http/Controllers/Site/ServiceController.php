<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Mail\ServiceRequestReceived;
use App\Models\FormSubmission;
use App\Models\Service;
use App\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ServiceController extends Controller
{
    public function index(TenantManager $manager)
    {
        if ($manager->isPortal()) {
            abort(404);
        }

        $services = Service::query()->where('is_active', true)->orderBy('order')->get();
        return view('site.services.index', compact('services'));
    }

    public function show(string $slug, TenantManager $manager)
    {
        if ($manager->isPortal()) {
            abort(404);
        }

        $service = Service::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('site.services.show', compact('service'));
    }

    public function submit(Request $request, string $slug, TenantManager $manager)
    {
        if ($manager->isPortal()) {
            abort(404);
        }

        $service = Service::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:32'],
            'subject' => ['nullable', 'string', 'max:60'],
            'message' => ['required', 'string', 'min:20', 'max:2000'],
            'consent' => ['accepted'],
        ], [
            'consent.accepted' => 'É necessário concordar com a Política de Privacidade.',
        ]);

        $submission = FormSubmission::create([
            'tenant_id' => $manager->id(),
            'service_id' => $service->id,
            'form_type' => 'servico',
            'name' => $data['name'],
            'email_encrypted' => Crypt::encryptString($data['email']),
            'phone' => $data['phone'] ?? null,
            'subject' => $service->title . ' (' . ($data['subject'] ?? '—') . ')',
            'message' => $data['message'],
            'status' => 'novo',
            'deadline_at' => now()->addBusinessDays(5),
            'ip_hash' => hash('sha256', $request->ip()),
            'metadata' => [
                'service_slug' => $service->slug,
                'service_title' => $service->title,
                'audience' => $data['subject'] ?? null,
                'user_agent' => substr((string) $request->userAgent(), 0, 240),
            ],
        ]);

        // Notificação por e-mail para a PR-6
        try {
            Mail::to(config('pr6.contact_email', 'pr6@uerj.br'))
                ->send(new ServiceRequestReceived($submission, $service));
        } catch (\Throwable $e) {
            Log::error('Falha ao enviar e-mail de serviço: ' . $e->getMessage());
        }

        return redirect()->route('services.show', $service->slug)
            ->with('service_submitted', true);
    }
}
