<?php

namespace App\Mail;

use App\Models\FormSubmission;
use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ServiceRequestReceived extends Mailable
{
    use Queueable;

    public function __construct(public FormSubmission $submission, public Service $service)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . ($this->service->tenant?->short_name ?? 'PR-6') . '] Nova solicitação: ' . $this->service->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-request',
            with: [
                'submission' => $this->submission,
                'service' => $this->service,
                'tenant' => $this->service->tenant,
            ],
        );
    }
}
