@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
<script src="/js/helpers/show-hidden.js"></script>

@liwewireStyles
@section('table')
    <div class="alert alert-success">
        Uwaga! możesz użyć tagu {idZamowienia} w wiadomości, który zostanie zastąpiony przez id zamówienia.
    </div>


    <livewire:low-order-quantity-alert-managment />

    @livewireScripts
@endsection
