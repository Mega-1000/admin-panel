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

        <button class="btn btn-primary">
            Zapisz
        </button>
    </form>
@endsection
