<?php

namespace App\Mail;

use App\Models\DataSubjectRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DataSubjectRequestReceived extends Mailable
{
    use Queueable;

    public function __construct(public DataSubjectRequest $request)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[LGPD] Nova solicitação de direitos do titular · ' . DataSubjectRequest::REQUEST_TYPES[$this->request->request_type],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lgpd-request',
            with: ['req' => $this->request],
        );
    }
}
