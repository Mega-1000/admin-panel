@extends('layouts.datatable')
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
    <form method="POST" action="{{route('transportPayment.update', ['delivererId' => $deliverer->id])}}">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="name">
                Nazwa
            </label>
            <input class="form-control" id="name" name="name" value="{{$deliverer->name}}">
        </div>

<!--        <div class="row">
            <div class="col-md-2">
                <label for="name">asdasdasd</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="" />
            </div>
            <div class="col-md-2">
                <label for="name">asdasdasd</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="" />
            </div>
        </div>-->



<!--        <label for="net_payment_column_number">
            Numer kolumny z kwotą (netto)
        </label>
        <input class="form-control" value="{{$deliverer->net_payment_column_number ?? ''}}" name="net_payment_column_number">-->

        <button type="submit" class="btn btn-primary">Zapisz</button>
    </form>
@endsection
