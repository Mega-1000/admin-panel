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
            @foreach($packageProducts as $product)
                <div>
                    <label for="product">{{ $product->name }}</label>
                    <img src="{{  $product->getImageUrl() }}"  alt="product image" width="100px" height="100px">

                    <label class="mt-4" for="quantity">Ilość</label>
                    <input
                        type="number"
                        class="form-control"
                        id="quantity"
                        placeholder="Ilość"
                        name="quantity[{{ $product->id }}]"
                    >
                </div>
            @endforeach

            <button class="btn mt-3 btn-primary">
                Zapisz
            </button>
        </form>
    </div>
@endsection
