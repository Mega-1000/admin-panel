@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> Stwórz nowe zamówienie
    </h1>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js" defer></script>
@endsection

@section('table')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="form-group" method="post" action="{{ route('admin-order-TWSU.create') }}">
        @csrf

        <label for="warehousesSymbols">Symbol magazynu</label>
        <select class="select2" data-live-search="true" name="warehousesSymbols">
            <option value="0">Wybierz symbol magazynu</option>
            @foreach($warehousesSymbols as $warehousesSymbol)
                <option value="{{ $warehousesSymbol }}">{{ $warehousesSymbol }}</option>
            @endforeach
        </select>

        <label for="client_email">
            Email klienta
        </label>
        <input name="client_email" id="client_email" type="text" class="form-control">

        <label for="purchase_value">
            Wartość zakupu brutto
        </label>
        <input name="purchase_value" id="purchase_value" type="text" class="form-control">

        <label for="consultant_description">
            Opis konsultanta
        </label>
        <input name="consultant_description" id="consultant_description" type="text" class="form-control">

        <button class="btn btn-primary">Zapisz</button>
    </form>
@endsection
