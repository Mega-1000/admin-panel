@extends('layouts.datatable')
@section('table')
    <div class="form-group">
        <label for="deliverers">Dodaj nowego dostawcę: </label>
        <a name="deliverers" class="btn btn-success" href="{{ route('transportPayment.createOrUpdate') }}">Dodaj</a>
    </div>
    @if($deliverers->count())
        <table class="table table-hover spacious-container">
            <thead>
            <tr>
                <th>
                    ID
                </th>
                <th>
                    Nazwa dostawcy
                </th>
                <th>
                    Numer kolumny z kwotą (netto)
                </th>
                <th>
                    Numer kolumny z kwotą (brutto)
                </th>
                <th>
                    Numer kolumny z listem przewozowym
                </th>
                <th>
                    Akcje
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($deliverers as $delivery)
                <tr>
                    <td>
                        {{ $delivery->id }}
                    </td>
                    <td>
                        {{ $delivery->name }}
                    </td>
                    <td>
                        {{ $delivery->net_payment_column_number }}
                    </td>
                    <td>
                        {{ $delivery->gross_payment_column_number_gross }}
                    </td>
                    <td>
                        {{ $delivery->letter_number_column_number }}
                    </td>
                    <td>
                        <a class="btn btn-success"
                           href="{{ route('transportPayment.createOrUpdate',['id' => $delivery->id]) }}">Edycja</a>
                        <a class="btn btn-danger"
                           href="{{ route('transportPayment.delete',['id' => $delivery->id]) }}">Usuń</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        Brak dostawców
    @endif
@endsection
