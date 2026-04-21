@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>

@section('table')
    @if($errors->any)
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endforeach
    @endif

    <x-discount-form
        :discount="$discount"
        :products="$products"
    />
@endsection
