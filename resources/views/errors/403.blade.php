@extends('errors.layout', ['title' => 'Acesso negado'])

@section('code', '403')
@section('icon')
    <div class="err-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
    </div>
@endsection
@section('heading', 'Acesso restrito')
@section('message')
    Você não tem permissão para acessar este recurso. Se acredita que isso é um erro, entre em contato com a equipe técnica da PR-6.
@endsection
