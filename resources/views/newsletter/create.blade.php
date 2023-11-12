@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
<script src="/js/helpers/show-hidden.js"></script>

@section('table')
    <form action="{{ route('newsletter.store') }}" method="POST">
        @csrf

        Kategoria
        <input type="text" class="form-control" name="category">

        <br>

        Symbol produktu
        <input type="text" class="form-control" name="product">

        Url aukcji
        <input type="text" class="form-control" name="auction_url">
        <br>

        Opis
        <input type="text" class="form-control" name="description">
        <br>

        <button class="btn btn-primary">
            Zapisz
        </button>
    </form>
@endsection
