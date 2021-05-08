@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-window-list"></i> @lang('bonus.title')
    </h1>
    <link href="{{ asset('css/views/bonus/style.css') }}" rel="stylesheet">
@endsection

@section('table')
    @can('create-bonus')
        <div class="modal fade" tabindex="-1" id="add_bonus_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Dodaj premię/potrącenie</h4>
                    </div>
                    <div class="modal-body">
                        <form action="#" id="add_new_bonus_form" method="POST">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <select name="user_id" required class="form-control">
                                    <option value=""></option>
                                    @foreach($users as $user)
                                        <option
                                            value="{{$user->id}}">{{$user->name}} {{$user->firstname}} {{$user->lastname}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Zadanie</label>
                                <input class="form-control" name="order_id">
                            </div>
                            <div class="form-group">
                                <label>Kwota (ujemna stanowi karę)</label>
                                <input class="form-control" required name="amount" type="number" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>Powód</label>
                                <input class="form-control" required name="cause" type="text">
                            </div>
                            <div class="form-group">
                                <label>Data nadania</label>
                                <input
                                    value="{{ Carbon\Carbon::now()->format("Y-m-d") }}"
                                    class="date-picker-bonus form-control" required name="date">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" form="add_new_bonus_form" class="btn btn-success pull-right">Utwórz
                        </button>
                        <button type="button" class="btn btn-default pull-right"
                                data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    @endcan
    @if($bonuses->count() === 0)
        <h3>Brak premii i potrąceń</h3>
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
