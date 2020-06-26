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
    <form method="POST" action="{{route('transportPayment.post')}}">
        {{ csrf_field() }}
        <div class="form-group">
            <input hidden name="id" value="{{$deliverer->id ?? null}}">
            <label for="nazwa">
                Nazwa
            </label>
            <input class="form-control" value="{{$deliverer->name ?? ''}}" name="name">
        </div>
        <label for="net_payment_column_number">
            Numer kolumny z kwotą (netto)
        </label>
        <input class="form-control" value="{{$deliverer->net_payment_column_number ?? ''}}" name="net_payment_column_number">
        <label for="gross_payment_column_number_gross">
            Numer kolumny z kwotą (brutto)
        </label>
        <input class="form-control" value="{{$deliverer->gross_payment_column_number_gross ?? ''}}" name="gross_payment_column_number_gross">
        <label for="letter_number_column_number">
            Numer kolumny z listem przewozowym
        </label>
        <input required class="form-control" value="{{$deliverer->letter_number_column_number ?? ''}}" name="letter_number_column_number">
        <button type="submit" class="btn btn-primary">Zapisz</button>
    </form>
@endsection
