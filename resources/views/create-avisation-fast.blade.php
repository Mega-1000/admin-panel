@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-move"></i> @lang('import.title')
    </h1>
@endsection

@section('app-content')
    <form action="{{ route('storeAvisation', $order->id) }}" method="POST">
        @csrf

        Kwota wpłaty
        <input type="text" class="form-control" name="declared_sum">
        <br>
        <br>


        Magazyn do awizacji

        <select class="form-control" name="warehouse-symbol">
            @foreach($order->items->first()->product->firm->warehouses as $warehouse)
                <option>{{ $warehouse->symbol }}</option>
            @endforeach
        </select>
        <br>
        <br>

        Rodzaj transportu
        <select class="form-control">
            <option value="1">
                Transport Fabryczny
            </option>
            <option value="2">
                Odbiór osobisty
            </option>
        </select>
        <br>

        <button  class="form-control" class="btn btn-primary">
            Zatwierdź
        </button>

    </form>
@endsection
