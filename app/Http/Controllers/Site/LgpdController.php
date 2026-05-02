<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\ConsentLog;
use App\Models\DataSubjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class LgpdController extends Controller
{
    public function privacy()
    {
        return view('site.lgpd.privacy');
    }

    public function rights()
    {
        return view('site.lgpd.rights');
    }

    public function storeConsent(Request $request)
    {
        $data = $request->validate([
            'consents' => ['required', 'array'],
            'consents.essential' => ['nullable', 'boolean'],
            'consents.analytics' => ['nullable', 'boolean'],
            'consents.marketing' => ['nullable', 'boolean'],
        ]);

        $session = $request->session()->getId() ?: bin2hex(random_bytes(16));
        $sessionHash = hash('sha256', $session);
        $ipHash = hash('sha256', $request->ip());
        $uaHash = hash('sha256', (string) $request->userAgent());

        foreach ($data['consents'] as $type => $granted) {
            ConsentLog::create([
                'session_hash' => $sessionHash,
                'consent_type' => $type,
                'granted' => (bool) $granted,
                'granted_at' => $granted ? now() : null,
                'revoked_at' => $granted ? null : now(),
                'ip_hash' => $ipHash,
                'user_agent_hash' => $uaHash,
            ]);
        }

        return response()->json([
            'ok' => true,
            'session_hash' => $sessionHash,
        ]);
    }

    public function storeRequest(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160'],
            'cpf' => ['required', 'string', 'max:20'],
            'request_type' => ['required', 'in:acesso,correcao,exclusao,portabilidade,revogacao_consentimento,oposicao'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'consent' => ['accepted'],
        ], [
            'consent.accepted' => 'É necessário concordar com a coleta dos dados desta solicitação.',
            'request_type.in' => 'Tipo de solicitação inválido.',
        ]);

        $cpfDigits = preg_replace('/\D/', '', $data['cpf']);
        if (! $this->validateCpf($cpfDigits)) {
            return back()->withErrors(['cpf' => 'CPF inválido. Verifique os números informados.'])->withInput();
        }

        DataSubjectRequest::create([
            'requester_name' => $data['name'],
            'email_encrypted' => Crypt::encryptString($data['email']),
            'cpf_encrypted' => Crypt::encryptString($cpfDigits),
            'request_type' => $data['request_type'],
            'description' => $data['description'],
            'status' => 'recebido',
            'deadline_at' => Carbon::now()->addDays(15),
            'ip_hash' => hash('sha256', $request->ip()),
        ]);

        return redirect()->route('lgpd.rights')->with('lgpd_submitted', true);
    }

    private function validateCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11) return false;
        if (preg_match('/(\d)\1{10}/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += (int) $cpf[$i] * (($t + 1) - $i);
            }
            $digit = ((10 * $sum) % 11) % 10;
            if ((int) $cpf[$t] !== $digit) return false;
        }
        return true;
    }
}
