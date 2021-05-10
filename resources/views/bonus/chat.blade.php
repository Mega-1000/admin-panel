@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> @lang('bonus.title')
    </h1>
    <link href="{{ asset('css/views/bonus/style.css') }}" rel="stylesheet">
@endsection

@section('table')
    <h3>Dyskusja: {{ $bonus->cause }}</h3>
    <h4>Zamówienie: {{ $bonus->order_id }}</h4>
    <h5>{{ $bonus->amount }} {{ $bonus->user->firstname }} {{ $bonus->user->lastname }}</h5>
    <hr>
    @if(count($chat))
        @foreach($chat as $message)
            <h6>{{ $message['name'] }}:</h6>
            <p>{{ $message['message'] }}</p>
        @endforeach
    @else
        <h5>Brak wiadomości</h5>
    @endif

    <hr>
    <form action="{{ route('bonus.send_message', ['id' => $bonus->id]) }}" method="post">
        {{ csrf_field() }}
        <div class="form-group">
            <textarea name="message" class="form-control" name="" id="" cols="30" rows="2"></textarea>
        </div>
        <div class="form-group">
            <button class="btn btn-primary">Wyślij</button>
        </div>
    </form>
@endsection
