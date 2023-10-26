@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
@section('table')
    <form action="{{ route('product-packets.update', $packet->id) }}" method="post">
        @csrf
        @method('put')
        <div class="form-group">
            <label for="packet_name">Nazwa</label>
            <input
                type="text"
                class="form-control"
                id="packet_name"
                name="packet_name"
                placeholder="Wpisz nazwę paczki"
                value="{{ $packet->packet_name }}"
            >
        </div>

        <div class="form-group">
            <label for="product_symbol">Symbol produktu</label>
            <input
                type="text"
                class="form-control"
                id="product_symbol"
                name="product_symbol"
                placeholder="Wpisz symbol produktu"
                value="{{ $packet->product_symbol }}"
            >
        </div>

        <div class="form-group">
            <label for="packet_products_symbols">Symbole produktów w paczce</label>
            <input
                value="{{ implode(',', json_decode($packet->packet_products_symbols)) }}"
                type="text"
                class="form-control"
                id="packet_products_symbols"
                name="packet_products_symbols"
                placeholder="Wpisz symbole produktów w paczce"
            >
        </div>

        <button type="submit" class="btn btn-primary">Dodaj</button>
    </form>
@endsection
