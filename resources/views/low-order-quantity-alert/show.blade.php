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

        <textarea name="message" class="form-control mt-5" placeholder="Wiadomość">{{ $message->message }}</textarea>

        <div>
            znaczniki z csv z kolumny
            13
            ktore bedą brane pod uwage do sumy ilości aczkolwiek kazdy nalezy oddzielic przcinkiem bez spacji
        </div>
        <input type="text" name="item_names" placeholder="Nazwy produktów" class="form-control mt-5" value="{{ $message->item_names }}">

        <div>
            ilosc sztuk zakupiona w calej ofercie produktow ktore maja znaczniki podane w polu 3
        </div>
        <input type="number" name="min_quantity" placeholder="Minimalna ilość" class="form-control mt-5" value="{{ $message->min_quantity }}">

        <div>
            Ilość godzin po których zostaną wysłane e mail na adres wlasciciela konta w/w wiaodmosc
        </div>
        <input type="number" name="delay_time" placeholder="Czas opóźnienia" class="form-control mt-5" value="{{ $message->delay_time }}">

        <button class="btn btn-primary mt-5">
            Zapisz
        </button>
    </form>
@endsection
