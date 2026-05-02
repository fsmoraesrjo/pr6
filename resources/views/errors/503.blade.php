@extends('errors.layout', ['title' => 'Em manutenção'])

@section('code', '503')
@section('icon')
    <div class="err-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
    </div>
@endsection
@section('heading', 'Em manutenção')
@section('message')
    O portal está passando por uma atualização programada. Voltamos em instantes com novidades.
@endsection
