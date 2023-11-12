@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
<script src="/js/helpers/show-hidden.js"></script>

@section('table')
    <form action="{{ route('newsletter.update', $newsletter) }}" method="POST">
        @csrf
        @method('PUT')

        Kategoria
        <input type="text" class="form-control" name="category" value="{{ $newsletter->category }}">
        <br>

        Symbol produktu
        <input type="text" class="form-control" name="product" value="{{ $newsletter->product }}">
        <br>

        Url aukcji
        <input type="text" class="form-control" name="auction_url" value="{{ $newsletter->auction_url }}">
        <br>

        Opis
        <input type="text" class="form-control" name="description" value="{{ $newsletter->description }}">
        <br>

        <button class="btn btn-primary">
            Zapisz
        </button>
    </form>
@endsection
