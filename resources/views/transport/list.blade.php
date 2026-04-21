@extends('layouts.datatable')
@section('table')
    <div class="form-group">
        <label for="deliverers">Dodaj nowego dostawcę: </label>
        <a name="deliverers" class="btn btn-success" href="{{ route('transportPayment.create') }}">Dodaj</a>
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
                    Akcje
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($deliverers as $deliverer)
                <tr>
                    <td>
                        {{ $deliverer->id }}
                    </td>
                    <td>
                        {{ $deliverer->name }}
                    </td>
                    <td>
                        <a class="btn btn-success"
                           href="{{ route('transportPayment.edit',['id' => $deliverer->id]) }}">Edycja</a>
                        <a class="btn btn-danger"
                           href="{{ route('transportPayment.delete',['id' => $deliverer->id]) }}"
                           onclick="return confirm('Czy na pewno chcesz usunąć wybranego dostawcę?')">Usuń</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        Brak dostawców
    @endif
@endsection
