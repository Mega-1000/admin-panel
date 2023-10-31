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

        <button class="btn btn-primary">
            Zapisz
        </button>
    </form>
@endsection
