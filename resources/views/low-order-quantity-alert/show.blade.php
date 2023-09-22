@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
<script src="/js/helpers/show-hidden.js"></script>
@section('table')
    <div class="alert alert-success">
        Uwaga! możesz użyć tagu {idZamowienia} w wiadomości, który zostanie zastąpiony przez id zamówienia.
    </div>

    <form action="{{ route('low-quantity-alerts.update', $message->id) }}" method="post">
        @csrf
        @method('PUT')

        <input type="text" name="title" placeholder="Tytuł" class="form-control" value="{{ $message->title }}">

        <textarea name="message" class="form-control mt-5" placeholder="Wiadomość">
            {{ $message->message }}
        </textarea>

        <input type="text" name="item_names" placeholder="Nazwy produktów" class="form-control mt-5" value="{{ $message->item_names }}">

        <input type="number" name="min_quantity" placeholder="Minimalna ilość" class="form-control mt-5" value="{{ $message->min_quantity }}">

        <input type="number" name="delay_time" placeholder="Czas opóźnienia" class="form-control mt-5" value="{{ $message->delay_time }}">

        <button class="btn btn-primary mt-5">
            Zapisz
        </button>
    </form>
@endsection
