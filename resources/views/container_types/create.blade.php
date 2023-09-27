@php use App\Enums\CourierName; @endphp
@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-company"></i>@if (empty($containerType))
            @lang('order_packages.container_create')
        @else
            @lang('order_packages.container_edit')
        @endif
        <a style="margin-left: 15px;" href="{{ action('ContainerTypesController@index') }}"
           class="btn btn-info install pull-right">
            <span>@lang('order_packages.container_list')</span>
        </a>
    </h1>
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
    @if (empty($containerType))
        <form action="{{ action('ContainerTypesController@store') }}" method="POST">
            @else
                <form action="{{ action('ContainerTypesController@update', [$containerType->id]) }}" method="POST">
                    {{ method_field('PUT')}}
                    @endif
                    {{ csrf_field() }}
                    <div class="firms-general" id="general">
                        <div class="form-group">
                            <label for="name">@lang('order_packages.form.container_type_name')</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   @if (!empty($containerType))  value="{{ $containerType->name }}"@endif>
                        </div>
                        <div class="form-group">
                            <label for="name">@lang('firms.form.role_symbol')</label>
                            <input type="text" class="form-control" id="symbol" name="symbol"
                                   @if (!empty($containerType))  value="{{ $containerType->symbol }}"@endif>
                        </div>
                        <div class="form-group">
                            <label for="provider_name">@lang('order_packages.form.container_type_provider')</label>
                            <select name="shipping_provider" class="form-control">
                                <option value="">Wybierz kuriera</option>
                                @foreach(CourierName::NAMES_FOR_DAY_CLOSE as $code => $courierName)
                                    <option
                                            @if (!empty($containerType) && $containerType->shipping_provider === $code)  selected="selected"
                                            @endif value="{{ $code }}">{{ $courierName }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(!empty($containerType) && $containerType->additional_informations)
                            <div class="form-group">
                                <label
                                        for="additional_informations">@lang('order_packages.form.container_type_additional_informations')</label>
                                <table>
                                    @foreach($containerType->additional_informations as $description => $value )
                                        <tr>
                                            <td class="text-right font-weight-700">{{ $description }}</td>
                                            <td style="padding-left: 10px"><?php echo is_bool($value) ? ($value === true ? 'TAK' : 'NIE') : $value ?></td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
                </form>
        @endsection
