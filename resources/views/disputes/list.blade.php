@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-character"></i> Dyskusje allegro
    </h1>
@endsection

@section('table')
    <table id="dataTable" class="table table-hover">
        <thead>
        <tr>
            <th>#</th>
            <th>Zamówienie</th>
            <th>Klient allegro</th>
            <th>Temat</th>
            <th>Status</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($disputes as $dispute)
            <tr class="{{ $dispute->unseen_changes ? 'font-weight-bold' : '' }}">
                <td>{{ $dispute->id }}</td>
                <td>{!!  $dispute->order !!}</td>
                <td>{{ $dispute->buyer_login }}</td>
                <td>{{ $dispute->subject }}</td>
                <td>{{ $dispute->status == 'ONGOING' ? 'Otwarta' : 'Zamknięta' }}</td>
                <td><a href="/admin/disputes/view/{{ $dispute->id }}" class="btn btn-default">Zobacz</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $disputes->render() }}
@endsection


@section('datatable-scripts')

@endsection
