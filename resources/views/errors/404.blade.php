@extends('errors.layout', ['title' => 'Página não encontrada'])

@section('code', '404')
@section('icon')
    <div class="err-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5M8 11h6"/></svg>
    </div>
@endsection
@section('heading', 'Página não encontrada')
@section('message')
    O endereço que você acessou não existe ou foi movido. Que tal voltar à home ou entrar em contato com a PR-6 para informar o erro?
@endsection
