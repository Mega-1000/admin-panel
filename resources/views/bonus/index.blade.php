@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> @lang('bonus.title')
    </h1>
    <link href="{{ asset('css/views/bonus/style.css') }}" rel="stylesheet">
@endsection

@section('table')

    @if($bonuses->count() === 0)
        <h3>Brak potrąceń</h3>
    @else

        <div>
            <label for="date-from-grid">Data od: </label>
            <input id="date-from-grid" class="date-picker-bonus date-range-filter">
            <label for="date-to-grid">Data do: </label>
            <input id="date-to-grid" class="date-picker-bonus date-range-filter">
        </div>
        <table id="dataTable" class="table table-hover">
            <thead>
            <tr>
                <th id="first-bonus-column">Użytkownik</th>
                <th>Powód</th>
                <th>Nr zamówienia (jeśli istnieje)</th>
                <th>Kwota</th>
                <th>Ocena szkodliwości</th>
                <th>Data nadania</th>
                <th>Akcje</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($bonuses as $bonus)
                <tr>
                    <td>{{ $bonus->user->name . ' ' . $bonus->user->firstname . ' ' . $bonus->user->lastname}}</td>
                    <td>{{ $bonus->cause }}</td>
                    <td>{{ $bonus->order_id }}</td>
                    <td>{{ $bonus->amount }}</td>
                    <td>{{ $bonus->points }}</td>
                    <td>{{ $bonus->date }}</td>
                    <td><form method="POST" action="{{ route('bonus.destroy') }}">
                            {{ csrf_field() }}
                            <input type="hidden" value="{{ $bonus->id }}" name="id" />
                            <button type="submit" class="btn btn-danger pull-right" style="margin-left:10px">Usuń</button>
                            <a href="{{ route('bonus.chat', ['id' => $bonus->id]) }}" class="btn btn-primary pull-right">Dyskusja</a>
                        </form></td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4" style="text-align:right">Suma:</th>
                <th></th>
            </tr>
            </tfoot>
        </table>
        <div class="dataTables_info" id="sum_info" role="status" aria-live="polite"></div>
        <div class="dataTables_info" id="pkt_info" role="status" aria-live="polite"></div>
    @endif
@endsection
@section('datatable-scripts')
    <script>
        $('.date-picker-bonus').datepicker({dateFormat: "yy-mm-dd"});
    </script>
    @parent
@endsection
@section('javascript')
    <script type="text/javascript" src="{{ URL::asset('js/helpers/data-tables-bonus-penalty.js') }}"></script>
    <script>
    </script>
    @parent
@endsection
