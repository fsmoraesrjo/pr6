@extends('emails.layout', ['subject' => 'Nova solicitação de serviço'])

@section('slot')
@php
    $deadline = $submission->deadline_at?->format('d/m/Y');
    $email = \Illuminate\Support\Facades\Crypt::decryptString($submission->email_encrypted);
@endphp

<h2 style="margin:0 0 8px;font-size:20px;color:#1F2937;">Nova solicitação de serviço</h2>
<p style="color:#4B5563;font-size:14px;margin:0 0 24px;line-height:1.5;">
    Foi registrada uma nova solicitação no portal {{ $tenant?->short_name ?? 'PR-6' }}.
    O prazo para resposta é <strong>{{ $deadline }}</strong>.
</p>

<table width="100%" cellpadding="10" cellspacing="0" style="border:1px solid #E7E2DE;border-radius:8px;background:#FAF7F5;font-size:14px;color:#1F2937;">
    <tr><td width="140" style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;">Serviço</td>
        <td><strong>{{ $service->title }}</strong></td></tr>
    <tr><td style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;">Solicitante</td>
        <td>{{ $submission->name }}</td></tr>
    <tr><td style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;">E-mail</td>
        <td><a href="mailto:{{ $email }}" style="color:#B92828;">{{ $email }}</a></td></tr>
    @if($submission->phone)
    <tr><td style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;">Telefone</td>
        <td>{{ $submission->phone }}</td></tr>
    @endif
    <tr><td style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;">Vínculo</td>
        <td>{{ $submission->subject }}</td></tr>
    <tr><td style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;vertical-align:top;">Mensagem</td>
        <td style="white-space:pre-wrap;line-height:1.55;">{{ $submission->message }}</td></tr>
    <tr><td style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;">Recebida em</td>
        <td>{{ $submission->created_at->format('d/m/Y \à\s H:i') }}</td></tr>
</table>

<p style="margin:24px 0 0;text-align:center;">
    <a href="{{ url('/admin') }}" style="display:inline-block;background:#B92828;color:#fff;padding:12px 24px;border-radius:999px;text-decoration:none;font-weight:600;font-size:14px;">
        Responder no painel
    </a>
</p>
@endsection
