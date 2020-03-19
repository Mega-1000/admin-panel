@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> Dodaj szablon
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
    <form action="{{ action('PackageTemplatesController@store') }}" method="POST" onsubmit="return validate(this);">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="data_template">Nazwa Szablonu Danych</label>
            <input type="text" class="form-control" id="name" name="name"
                   value="{{ old('name') }}">
        </div>
        <div class="form-group">
            <label for="data_template">Dodatkowe Informacje Dotyczące Szablonu</label>
            <input type="text" class="form-control" id="info" name="info"
                   value="{{ old('info') }}">
        </div>
        <div class="form-group">
            <label for="data_template">Symbol szablonu</label>
            <input type="text" class="form-control" id="symbol" name="symbol"
                   value="{{ old('symbol') }}">
        </div>
        <div class="form-group">
            <label for="data_template">@lang('order_packages.form.displayed_name')</label>
            <input type="text" class="form-control" id="displayed_name" name="displayed_name"
                   value="{{ old('displayed_name') }}">
        </div>
        <div class="firms-general" id="orderPayment">
            <div class="form-group">
                <label for="size_a">@lang('order_packages.form.size_a')</label>
                <input type="number" class="form-control" id="sizeA" name="sizeA"
                       value="{{ old('size_a') }}">
            </div>
            <div class="form-group">
                <label for="size_b">@lang('order_packages.form.size_b')</label>
                <input type="number" class="form-control" id="sizeB" name="sizeB"
                       value="{{ old('size_b') }}">
            </div>
            <div class="form-group">
                <label for="size_c">@lang('order_packages.form.size_c')</label>
                <input type="number" class="form-control" id="sizeC" name="sizeC"
                       value="{{ old('size_c') }}">
            </div>
            <div class="form-group">
                <label for="size_c">@lang('order_packages.form.accept_time')</label>
                <input type="time" class="form-control" id="accept_time" name="accept_time">
            </div>
            <div class="form-group">
                <label for="size_c">@lang('order_packages.form.accept_time_info')</label>
                <input type="text" class="form-control" id="accept_time_info" name="accept_time_info"
                       value="{{ old('accept_time_info') }}">
            </div>
            <div class="form-group">
                <label for="size_c">@lang('order_packages.form.max_time')</label>
                <input type="time" class="form-control" id="max_time" name="max_time">
            </div>
            <div class="form-group">
                <label for="size_c">@lang('order_packages.form.max_time_info')</label>
                <input type="text" class="form-control" id="max_time_info" name="max_time_info"
                       value="{{ old('max_time_info') }}">
            </div>
            <div class="form-group">
                <label for="service_courier_name">@lang('order_packages.form.service_courier_name')</label>
                <select class="form-control" id="service_courier_name" name="service_courier_name">
                    <option {{ old('delivery_courier_name') == 'INPOST' ? 'selected="selected"' : '' }} value="INPOST">
                        INPOST
                    </option>
                    <option
                        {{ old('delivery_courier_name') == 'POCZTEX' ? 'selected="selected"' : '' }} value="POCZTEX">
                        POCZTEX
                    </option>
                    <option {{ old('delivery_courier_name') == 'DPD' ? 'selected="selected"' : '' }} value="DPD">DPD
                    </option>
                    <option
                        {{ old('delivery_courier_name') == 'APACZKA' ? 'selected="selected"' : '' }} value="APACZKA">
                        APACZKA
                    </option>
                    <option {{ old('delivery_courier_name') == 'JAS' ? 'selected="selected"' : '' }} value="JAS">JAS
                    </option>
                    <option {{ old('delivery_courier_name') == 'GIELDA' ? 'selected="selected"' : '' }} value="GIELDA">
                        GIELDA
                    </option>
                    <option
                        {{ old('delivery_courier_name') == 'ODBIOR_OSOBISTY' ? 'selected="selected"' : '' }} value="ODBIOR_OSOBISTY">
                        ODBIÓR OSOBISTY
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="delivery_courier_name">@lang('order_packages.form.delivery_courier_name')</label>
                <select class="form-control" id="delivery_courier_name" name="delivery_courier_name">
                    <option {{ old('delivery_courier_name') == 'INPOST' ? 'selected="selected"' : '' }} value="INPOST">
                        INPOST
                    </option>
                    <option
                        {{ old('delivery_courier_name') == 'POCZTEX' ? 'selected="selected"' : '' }} value="POCZTEX">
                        POCZTEX
                    </option>
                    <option {{ old('delivery_courier_name') == 'DPD' ? 'selected="selected"' : '' }} value="DPD">DPD
                    </option>
                    <option
                        {{ old('delivery_courier_name') == 'APACZKA' ? 'selected="selected"' : '' }} value="APACZKA">
                        APACZKA
                    </option>
                    <option {{ old('delivery_courier_name') == 'JAS' ? 'selected="selected"' : '' }} value="JAS">JAS
                    </option>
                    <option {{ old('delivery_courier_name') == 'GIELDA' ? 'selected="selected"' : '' }} value="GIELDA">
                        GIELDA
                    </option>
                    <option
                        {{ old('delivery_courier_name') == 'ODBIOR_OSOBISTY' ? 'selected="selected"' : '' }} value="ODBIOR_OSOBISTY">
                        ODBIÓR OSOBISTY
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="weight">@lang('order_packages.form.real_weight')</label>
                <input type="text" class="form-control" id="weight" name="weight"
                       value="{{ old('weight') }}">
            </div>
            <div class="form-group">
                <label for="container_type">@lang('order_packages.form.container_type')</label><br/>
                <select class="form-control" id="container_type" name="container_type">
                    <option {{old('container_type') === 'POLPALETA' ? 'selected="selected"' : ''}} value="POLPALETA">
                        PÓŁPALETA 60x80
                    </option>
                    <option {{old('container_type') === 'EUR' ? 'selected="selected"' : ''}} value="EUR">PALETA 680x120
                    </option>
                    <option {{old('container_type') === 'INNA' ? 'selected="selected"' : ''}} value="INNA">PALETA
                        100x120
                    </option>
                    <option {{old('container_type') === 'PACZ' ? 'selected="selected"' : ''}} value="PACZ">PACZKA
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="shape">@lang('order_packages.form.shape')</label><br/>
                <input type="text" id="shape" name="shape" class="form-control" value="{{ old('shape') }}">
            </div>
            <div class="form-group">
                <label for="notices">Maksymalna ilość znaków w uwagach do spedycji</label>
                <input type="number" id="notice_max_lenght" name="notice_max_lenght" class="form-control"
                       value="{{ old('notice_max_lenght') }}">
            </div>
            <div class="form-group">
                <input type="hidden" name="status" value="NEW">
            </div>
            <div class="form-group">
                <label for="content">@lang('order_packages.form.content')</label>
                <input type="text" class="form-control" id="content" name="content"
                       value="Materiały budowlane">
            </div>
            <div class="form-group">
                <label for="cost_for_client">Koszt pobrania</label>
                <input type="number" step=".01" class="form-control" id="cod_cost" name="cod_cost"
                       value="{{ old('cod_cost') }}">
            </div>
            <div class="form-group">
                <label for="cost_for_client">@lang('order_packages.form.cost_for_client')</label>
                <input type="number" step=".01" class="form-control" id="approx_cost_client" name="approx_cost_client"
                       value="{{ old('approx_cost_client') }}">
            </div>
            <div class="form-group">
                <label for="cost_for_company">@lang('order_packages.form.cost_for_company')</label>
                <input type="number" step=".01" class="form-control" id="approx_cost_firm" name="approx_cost_firm"
                       value="{{ old('approx_cost_firm') }}">
            </div>
            <div class="form-group">
                <label for="cost_for_company">@lang('order_packages.form.max_weight')</label>
                <input type="number" step=".01" class="form-control" id="max_weight" name="max_weight"
                       value="{{ old('max_weight') }}">
            </div>
            <div class="form-group">
                <label for="cost_for_company">@lang('order_packages.form.volume_factor')</label>
                <input type="number" step=".01" class="form-control" id="volume" name="volume"
                       value="{{ old('volume') }}">
            </div>
            <div class="form-group">
                <label for="list_order">@lang('order_packages.form.list_order')</label>
                <input type="number" class="form-control" id="list_order"
                       name="list_order"
                       value="{{ old('list_order') }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
@endsection
@section('scripts')
    <script>
        $(document).on("click", ".open", function () {
            let id = $(this).data('package-id');
            let value = $(this).data('package-value');
            $('.package_id').text(id);
            $('#packageId').val(id);
            $('#modalPackageValue').val(value);
            $('#packageDialog').modal('show');
        });

        function validate(form) {
            console.log(payments);
            if (paymentsSum < 2 && promisedPaymentsSum > 2) {
                if (confirm('Zlecenie posiada wyłącznie zaliczkę deklarowaną. Czy chcesz kontynuować przy jej użyciu?')) {
                    $('#shouldTakePayment').val(1);
                    return true;
                } else {
                    return false;
                }
            }
            if (promisedPaymentsSum == paymentsSum) {
                $('#shouldTakePayment').val(2);
                return true;
            }
            if (payments.length > 0) {
                if (Math.abs(promisedPaymentsSum - paymentsSum) > 2 && Math.abs(promisedPaymentsSum - paymentsSum) < -2) {
                    if (confirm('Zaliczka deklarowana posiada inną wartość niż zaliczka zaksięgowana. System uwzględni zaliczkę zaksięgowaną.')) {
                        $('#shouldTakePayment').val(3);
                        return true;
                    } else {

                        return false;
                    }
                }
            }
        }
    </script>
    <script src="{{URL::asset('js/jscolor.js')}}"></script>
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');


    </script>
@endsection
