@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-character"></i> Dyskusja z {{ $dispute->buyer_login }}: {{ $dispute->subject }}
    </h1>
@endsection

@section('table')

    <ul class="chat">
        @foreach(array_reverse($messages) as $message)
            <li class="{{ $message['author']['role'] }}">
                <b>{{ $message['author']['login'] }}</b>
                <small>{{ $message['author']['role'] == 'BUYER' ? '(kupujący)' : '(sprzedawca)' }}</small>:
                <span class="pull-right"><small>{{ (new \Carbon\Carbon($message['createdAt'])) }}</small></span>
                <br>
                {{ $message['text'] }}
                <br>
                @if(array_key_exists('attachment', $message))
                    <a target="_blank" href="{{ $message['attachment']['url'] }}">{{ $message['attachment']['fileName'] }}</a>
                @endif
            </li>
        @endforeach
    </ul>
    @if($dispute->status == 'ONGOING')
    <form method="post" action="/admin/disputes/send/{{ $dispute->id }}">
        {{ csrf_field() }}
        <textarea name="text" class='form-control' name="" id="" cols="30" rows="3"></textarea>
        <button class="btn btn-primary">Wyślij</button>
    </form>
    @endif

    <style>
        ul.chat {
            list-style-type: none;
            padding: 0;
        }

        ul.chat li {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        ul.chat li.SELLER {
            border: 1px solid #ddd;
            background: #eee;
        }

        ul.chat li.BUYER {
            border: 1px solid #ddf;
            background: #eef;
        }
    </style>
@endsection


@section('datatable-scripts')

@endsection
