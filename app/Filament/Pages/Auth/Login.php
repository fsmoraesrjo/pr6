<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
                ViewField::make('hcaptcha_token')
                    ->view('filament.auth.hcaptcha')
                    ->dehydrated(true),
            ])
            ->statePath('data');
    }

    public function authenticate(): ?\Filament\Http\Responses\Auth\Contracts\LoginResponse
    {
        $sitekey = config('services.hcaptcha.sitekey');
        $secret = config('services.hcaptcha.secret');

        if ($sitekey && $secret) {
            $token = request()->input('h-captcha-response')
                ?: ($this->data['hcaptcha_token'] ?? null);

            if (! $token) {
                throw ValidationException::withMessages([
                    'data.email' => 'Resolva o desafio de segurança antes de prosseguir.',
                ]);
            }

            $response = Http::asForm()->timeout(8)->post('https://api.hcaptcha.com/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            if (! ($response->json('success') === true)) {
                throw ValidationException::withMessages([
                    'data.email' => 'Falha na verificação do desafio. Tente novamente.',
                ]);
            }
        }

        return parent::authenticate();
    }
}
