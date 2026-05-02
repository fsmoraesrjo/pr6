<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class DataSubjectRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'deadline_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public const REQUEST_TYPES = [
        'acesso' => 'Acesso aos dados',
        'correcao' => 'Correção de dados',
        'exclusao' => 'Exclusão de dados',
        'portabilidade' => 'Portabilidade',
        'revogacao_consentimento' => 'Revogação de consentimento',
        'oposicao' => 'Oposição ao tratamento',
    ];

    public const STATUSES = [
        'recebido' => 'Recebido',
        'em_analise' => 'Em análise',
        'concluido' => 'Concluído',
        'rejeitado' => 'Rejeitado',
    ];

    /** Atributo virtual que descriptografa o e-mail apenas no admin. */
    protected function emailPlain(): Attribute
    {
        return Attribute::get(fn () => $this->email_encrypted ? Crypt::decryptString($this->email_encrypted) : null);
    }

    protected function cpfPlain(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->cpf_encrypted) return null;
            $cpf = Crypt::decryptString($this->cpf_encrypted);
            // mascara mostrando apenas 3 e 2 dígitos: 123.***.***-89
            return strlen($cpf) === 11
                ? substr($cpf, 0, 3) . '.***.***-' . substr($cpf, 9, 2)
                : $cpf;
        });
    }

    public function isOverdue(): bool
    {
        return $this->deadline_at && $this->status === 'recebido' && $this->deadline_at->isPast();
    }

    public function daysRemaining(): int
    {
        return $this->deadline_at ? now()->diffInDays($this->deadline_at, false) : 0;
    }
}
