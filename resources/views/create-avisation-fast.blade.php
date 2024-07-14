@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-move"></i> @lang('import.title')
    </h1>
@endsection

@section('app-content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <form action="{{ route('storeAvisation', $order->id) }}" method="POST">
                        @csrf

                        Wartość wszystkich wpłat zaksięgowanych i deklarowanych które klient wykonał na ten moment
                        <input type="text" class="form-control" name="declared_sum">
                        <br>
                        <br>

                        Data wpłaty
                        <input type="datetime-local" class="form-control" name="declared_date">
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

                        Pracownik odpowiedzialny za awizację
                        <select class="form-control" name="warehouse-symbol">
                            @foreach($order->items->first()->product->firm->employees as $employee)
                                <option>{{ $employee->email }}</option>
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

                        <button  class="form-control" class="btn btn-primary" style="background-color: #00acee">
                            Zatwierdź
                        </button>

                    </form>
            </div>
        </div>
    </div>
    </div>
@endsection
