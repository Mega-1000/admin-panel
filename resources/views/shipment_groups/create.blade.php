@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> Tworzenie grupy przesyłek
        <a style="margin-left: 15px;" href="{{ action('ShipmentGroupController@index') }}"
           class="btn btn-info install pull-right">
            <span>Grupy przesyłek</span>
        </a>
    </h1>
    <style>
        .tags {
            width: 100%;
        }

        .tag {
            width: 50%;
            float: right;
        }
    </style>
@endsection

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
    <form action="{{ action('ShipmentGroupController@store') }}" method="POST">
        {{ csrf_field() }}
        <div class="row">
            <div class="form-group col-md-6">
                <label for="courier_name" class="col-md-4">Nazwa kuriera</label>
                <div class="col-md-6">
                    <select class="form-control text-uppercase " name="courier_name">
                        @foreach(\App\Enums\CourierName::NAMES_FOR_DAY_CLOSE as $code => $courierName)
                            <option value="{{ $code }}">{{ $courierName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="sent" class="col-md-4">Wysłana</label>
                <div class="col-md-6">
                    <input type="checkbox" id="sent" name="sent">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="package_type" class="col-md-4">Typ paczek</label>
                <div class="col-md-6">
                    <select class="form-control text-uppercase" name="package_type">
                        <option value="">BRAK</option>
                        <option value="DPD_D">DPD_D</option>
                        <option value="POCZTEX">POCZTEX</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="closed" class="col-md-4">Zamknięta</label>
                <div class="col-md-6">
                    <input type="checkbox" id="closed" name="closed">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="lp" class="col-md-4">Lp</label>
                <div class="col-md-6">
                    <input type="number" class="form-control" id="lp" name="lp"
                           value="{{old('lp')}}">
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="shipment_date" class="col-md-4">Data wysyłki</label>
                <div class="col-md-6">
                    <input type="date" class="form-control" id="shipment_date" name="shipment_date"
                           value="{{ old('shipment_date') }}">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@section('scripts')
    <script src="{{URL::asset('js/jscolor.js')}}"></script>
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');

        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/statuses/'>Statusy</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
    </script>
@endsection
