@extends('layouts.datatable')
<script src="https://cdn.tailwindcss.com" ></script>
<script src="/js/helpers/show-hidden.js"></script>
@liwewireStyles
@section('table')
    <div class="alert alert-success">
        Uwaga! możesz użyć tagu {idZamowienia} w wiadomości, który zostanie zastąpiony przez id zamówienia.
        <br>
        Oraz {linkiDoGazetki} aby wygenerować linki do gazetki.
    </div>


    <livewire:low-order-quantity-alert-management :alertId="$message->id" />

    @livewireScripts
@endsection
