@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        Stwórz produkt pakowy
    </h1>
@endsection

@section('app-content')
    <div class="panel panel-bordered panel-body" style="margin: 0 auto; width: 94%">
        <form action="{{ route('storePackageProductOrder', ['order' => $order->id]) }}" method="post">
            <div>
                <label for="name">Odejmij z kosztów transportu</label>
                <input type="checkbox" name="subtract-from-shipping-cost">

                <label for="name">Nie licz cen</label>
                <input type="checkbox" name="do-not-count-price">

                <label for="name">ceny towarów pakowych 0,01</label
                <input type="checkbox" name="package-products-price-0-01">
            </div>

            @csrf
            <label for="product">Produkt</label>
            <div style="display: flex;">
                @foreach($packageProducts as $product)
                    <div style="display: flex">
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
            </div>

            <button class="btn mt-3 btn-primary">
                Zapisz
            </button>
        </form>
    </div>
@endsection
