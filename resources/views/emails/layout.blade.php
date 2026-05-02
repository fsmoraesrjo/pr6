<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>{{ $subject ?? 'PR-6 UERJ' }}</title>
</head>
<body style="margin:0;background:#FAF7F5;font-family:Arial,sans-serif;color:#1F2937;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#FAF7F5;padding:24px 0;">
    <tr><td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
            <tr><td style="background:linear-gradient(135deg,#B92828,#8E1B1B);padding:24px;text-align:center;">
                <strong style="color:#fff;font-size:20px;letter-spacing:-0.02em;">PR-6 UERJ</strong>
                <div style="color:rgba(255,255,255,.85);font-size:12px;text-transform:uppercase;letter-spacing:0.12em;margin-top:4px;">Pró-Reitoria de Planejamento e Gestão</div>
            </td></tr>
            <tr><td style="padding:32px 24px;">@yield('slot')</td></tr>
            <tr><td style="background:#F2EDE9;padding:16px 24px;border-top:1px solid #E7E2DE;font-size:11px;color:#6B7280;text-align:center;">
                © {{ date('Y') }} UERJ · Pró-Reitoria de Planejamento e Gestão · <a href="https://pr6.lumislabs.com.br" style="color:#B92828;text-decoration:none;">pr6.lumislabs.com.br</a>
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
