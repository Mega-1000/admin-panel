@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        Stwórz produkt pakowy
    </h1>
@endsection

@section('app-content')
    <div class="panel panel-bordered panel-body" style="margin: 0 auto; width: 94%">
        <form action="{{ route('storePackageProductOrder', ['order' => $order->id]) }}" method="post">
            @csrf
            <label for="product">Produkt</label>
            <select class="form-control" name="product_id">
                @foreach($packageProducts as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>

            <label class="mt-4" for="quantity">Ilość</label>
            <input type="number" class="form-control" name="quantity" id="quantity" placeholder="Ilość">

            <button class="btn mt-3 btn-primary">
                Zapisz
            </button>
        </form>
    </div>
@endsection
