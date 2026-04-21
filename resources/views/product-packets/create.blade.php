@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
@section('table')
    <form action="{{ route('product-packets.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="packet_name">Nazwa</label>
            <input type="text" class="form-control" id="packet_name" name="packet_name" placeholder="Wpisz nazwę paczki">
        </div>

        <div class="form-group">
            <label for="product_symbol">Symbol produktu</label>
            <input type="text" class="form-control" id="product_symbol" name="product_symbol" placeholder="Wpisz symbol produktu">
        </div>

        <div class="form-group">
            <label for="packet_products_symbols">Symbole produktów w paczce</label>
            <input type="text" class="form-control" id="packet_products_symbols" name="packet_products_symbols" placeholder="Wpisz symbole produktów w paczce">
        </div>

        <button type="submit" class="btn btn-primary">Dodaj</button>
    </form>
@endsection
