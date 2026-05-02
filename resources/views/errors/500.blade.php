@extends('errors.layout', ['title' => 'Erro interno'])

@section('code', '500')
@section('icon')
    <div class="err-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0zM12 9v4M12 17h.01"/></svg>
    </div>
@endsection
@section('heading', 'Algo deu errado do nosso lado')
@section('message')
    Tivemos um erro inesperado ao processar sua solicitação. Nossa equipe técnica foi notificada. Tente novamente em alguns instantes ou volte à home.
@endsection
