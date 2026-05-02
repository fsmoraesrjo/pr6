@extends('emails.layout', ['subject' => 'Nova solicitação LGPD'])

@section('slot')
@php
    $email = \Illuminate\Support\Facades\Crypt::decryptString($req->email_encrypted);
    $cpfDigits = \Illuminate\Support\Facades\Crypt::decryptString($req->cpf_encrypted);
    $cpfMasked = strlen($cpfDigits) === 11 ? substr($cpfDigits, 0, 3) . '.***.***-' . substr($cpfDigits, 9, 2) : '—';
@endphp

<h2 style="margin:0 0 8px;font-size:20px;color:#1F2937;">Solicitação LGPD recebida</h2>
<p style="color:#4B5563;font-size:14px;margin:0 0 16px;line-height:1.5;">
    Um titular de dados exerceu um direito previsto na Lei 13.709/2018.
</p>
<p style="background:#FEF3C7;border-left:4px solid #C9A35B;padding:10px 14px;color:#1F1810;font-size:13px;margin:0 0 24px;line-height:1.5;">
    ⏱️ <strong>Prazo legal: até {{ $req->deadline_at?->format('d/m/Y') }}</strong> ({{ now()->diffInDays($req->deadline_at, false) }} dias).
</p>

<table width="100%" cellpadding="10" cellspacing="0" style="border:1px solid #E7E2DE;border-radius:8px;background:#FAF7F5;font-size:14px;color:#1F2937;">
    <tr><td width="140" style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;">Tipo</td>
        <td><strong>{{ \App\Models\DataSubjectRequest::REQUEST_TYPES[$req->request_type] }}</strong></td></tr>
    <tr><td style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;">Solicitante</td>
        <td>{{ $req->requester_name }}</td></tr>
    <tr><td style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;">CPF (mascarado)</td>
        <td>{{ $cpfMasked }}</td></tr>
    <tr><td style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;">E-mail</td>
        <td><a href="mailto:{{ $email }}" style="color:#B92828;">{{ $email }}</a></td></tr>
    <tr><td style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;vertical-align:top;">Descrição</td>
        <td style="white-space:pre-wrap;line-height:1.55;">{{ $req->description }}</td></tr>
    <tr><td style="color:#6B7280;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;">Recebida em</td>
        <td>{{ $req->created_at->format('d/m/Y \à\s H:i') }}</td></tr>
</table>

<p style="margin:24px 0 0;text-align:center;">
    <a href="{{ url('/admin') }}" style="display:inline-block;background:#B92828;color:#fff;padding:12px 24px;border-radius:999px;text-decoration:none;font-weight:600;font-size:14px;">
        Responder no painel
    </a>
</p>

<p style="margin:24px 0 0;font-size:12px;color:#6B7280;line-height:1.5;border-top:1px dashed #E7E2DE;padding-top:16px;">
    Este e-mail é enviado ao Encarregado pelo Tratamento de Dados Pessoais (DPO) da UERJ. Os dados do titular estão criptografados em repouso e foram descriptografados apenas para esta notificação.
</p>
@endsection
