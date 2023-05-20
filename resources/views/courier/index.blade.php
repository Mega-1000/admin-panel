@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-list"></i> Lista kurierów
    </h1>
@endsection

@section('table')

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Pozycja</th>
                <th>Aktywny</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($couriers as $courier)
            <tr>
                <td>{{$courier->courier_name}}</td>
                <td>{{$courier->item_number}}</td>
                <td>@if($courier->active==1)aktywny @else nieaktywny @endif</td>
                <td>
                    <a style="text-decoration: none;" href="{{ action('CourierController@edit',[$courier->id]) }}" class="btn btn-warning btn-xs"><span>Edytuj</span></a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
