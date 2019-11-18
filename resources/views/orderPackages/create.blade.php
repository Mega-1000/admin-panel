@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-tag"></i> @lang('order_packages.create')
        <a style="margin-left: 15px;" href="{{ action('OrdersController@edit', ["id" => $id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('order_packages.list')</span>
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
        .wrong-difference {
            color: orange;
        }
        .valid-difference {
            color: black;
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
    <form action="{{ action('OrdersPackagesController@store') }}" method="POST" onsubmit="return validate(this);">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="data_template">@lang('order_packages.form.data_template')</label>
            <select class="form-control text-uppercase" id="data_template">
                <option value="" selected="selected"></option>
                @foreach($templateData as $templateKey => $template)
                    <option value="{{ $templateKey }}">{{ $template['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="firms-general" id="orderPayment">
            <div class="form-group">
                <label for="size_a">@lang('order_packages.form.size_a')</label>
                <input type="text" class="form-control" id="size_a" name="size_a"
                       value="{{ old('size_a') }}">
            </div>
            <div class="form-group">
                <label for="size_b">@lang('order_packages.form.size_b')</label>
                <input type="text" class="form-control" id="size_b" name="size_b"
                       value="{{ old('size_b') }}">
            </div>
            <div class="form-group">
                <label for="size_c">@lang('order_packages.form.size_c')</label>
                <input type="text" class="form-control" id="size_c" name="size_c"
                       value="{{ old('size_c') }}">
            </div>
            <div class="form-group">
                <label for="shipment_date">@lang('order_packages.form.shipment_date')</label><br/>
                <input type="text" id="shipment_date" name="shipment_date" class="form-control default-date-picker-now"
                       value="{{ old('shipment_date') }}">
            </div>
            <div class="form-group">
                <label for="delivery_date">@lang('order_packages.form.delivery_date')</label><br/>
                <input type="text" id="delivery_date" name="delivery_date" class="form-control default-date-picker-now"
                       value="{{ old('delivery_date') }}">
            </div>
            <div class="form-group">
                <label for="service_courier_name">@lang('order_packages.form.service_courier_name')</label>
                <select class="form-control" id="service_courier_name" name="service_courier_name">
                    <option {{ old('delivery_courier_name') == 'INPOST' ? 'selected="selected"' : '' }} value="INPOST">
                        INPOST
                    </option>
                    <option {{ old('delivery_courier_name') == 'POCZTEX' ? 'selected="selected"' : '' }} value="POCZTEX">
                        POCZTEX
                    </option>
                    <option {{ old('delivery_courier_name') == 'DPD' ? 'selected="selected"' : '' }} value="DPD">DPD
                    </option>
                    <option {{ old('delivery_courier_name') == 'APACZKA' ? 'selected="selected"' : '' }} value="APACZKA">
                        APACZKA
                    </option>
                    <option {{ old('delivery_courier_name') == 'JAS' ? 'selected="selected"' : '' }} value="JAS">JAS
                    </option>
                    <option {{ old('delivery_courier_name') == 'GIELDA' ? 'selected="selected"' : '' }} value="GIELDA">
                        GIELDA
                    </option>
                    <option {{ old('delivery_courier_name') == 'ODBIOR_OSOBISTY' ? 'selected="selected"' : '' }} value="ODBIOR_OSOBISTY">
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
                    <option {{ old('delivery_courier_name') == 'POCZTEX' ? 'selected="selected"' : '' }} value="POCZTEX">
                        POCZTEX
                    </option>
                    <option {{ old('delivery_courier_name') == 'DPD' ? 'selected="selected"' : '' }} value="DPD">DPD
                    </option>
                    <option {{ old('delivery_courier_name') == 'APACZKA' ? 'selected="selected"' : '' }} value="APACZKA">
                        APACZKA
                    </option>
                    <option {{ old('delivery_courier_name') == 'JAS' ? 'selected="selected"' : '' }} value="JAS">JAS
                    </option>
                    <option {{ old('delivery_courier_name') == 'GIELDA' ? 'selected="selected"' : '' }} value="GIELDA">
                        GIELDA
                    </option>
                    <option {{ old('delivery_courier_name') == 'ODBIOR_OSOBISTY' ? 'selected="selected"' : '' }} value="ODBIOR_OSOBISTY">
                        ODBIÓR OSOBISTY
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="weight">@lang('order_packages.form.weight')</label>
                <input type="text" class="form-control" id="weight" name="weight"
                       value="{{ old('weight') }}">
            </div>
            <div class="form-group">
                <label for="quantity">@lang('order_packages.form.quantity')</label><br/>
                <input type="text" id="quantity" name="quantity" class="form-control" value="{{ old('quantity') }}">
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

                <table id="paymentsTable" class="table table-hover" style="float: left;">
                    <thead>
                    <tr>
                        <th>Typ zlecenia</th>
                        <th>ID zlecenia</th>
                        <th>Wartość zlecenia</th>
                        <th>Zaliczki zaksięgowane</th>
                        <th>Zaliczki deklarowane</th>
                        <th>Pozostało do zapłaty <br/> przed wskazaniem pobrania <br/> na teraz nadawanym LP</th>
			<th>Pobrania wskazane w już nadanych LP</th>
			<th>Kwota pobrania w tym LP <br/> dla bilansu wszystkich zleceń <br/> (głównego oraz połączonych)</th>
			<th>Pozostało do pobrania <br/> po uwzględnieniu obecnego LP <br/> dla bilansu wszystkich zleceń <br/> (głównego oraz połączonych) </th>
                        <th>Listy przewozowe</th>
                        <th>Rodzaj kuriera</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
			@php
				$toPaySum = $order->toPayPackages();
				$sumOfGrossValues = $order->getSumOfGrossValues();
                                        $bookedPaymentsSum = $order->bookedPaymentsSum();
                                        $promisePaymentsSum = $order->promisePaymentsSum();
			@endphp
                        <td>Główne</td>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->getSumOfGrossValues() }}</td>
                        <td>{{ $order->bookedPaymentsSum() }}</td>
                        <td>{{ $order->promisePaymentsSum() }}</td>
                        <td>{{ $order->toPayPackages() }}</td>
			<td>
			                            @foreach($order->packages as $package)
                                @if($package->status == 'CANCELLED' || $package->status == 'WAITING_FOR_CANCELLED')

                                @else
                                <p style="display: inline-block;">{{$package->cash_on_delivery }}</p>
				@if($package->status == 'SENDING' || $package->status == 'DELIVERED')
				@else
				 <a type="button" class="open" style="text-decoration: none;display: inline-block;" data-package-id="{{ $package->id }}" data-package-value="{{ $package->cash_on_delivery }}">
                                    Zmień
                                </a>
				@endif
				<br/>
                                @endif
                            @endforeach
			</td>
                            <td>
                            </td>
			<td></td>
                        <td style="vertical-align: top;">
                            @foreach($order->packages as $package)
                                @if($package->status == 'CANCELLED' || $package->status == 'WAITING_FOR_CANCELLED')

                                @else
                                    <p>{{$package->letter_number}}</p>

                                @endif
                            @endforeach
                        </td>
                        <td  style="vertical-align: top;">
                            @foreach($order->packages as $package)
				@if($package->status == 'CANCELLED' || $package->status == 'WAITING_FOR_CANCELLED')

                                @else
                                <p>{{$package->delivery_courier_name}}</p>
				@endif
                            @endforeach
                        </td>
                    </tr>
                    @foreach($connectedOrders as $connectedOrder)
                        <tr>
                            <td>Połączone</td>
                            <td>{{ $connectedOrder->id }}</td>
                            <td>{{ $connectedOrder->getSumOfGrossValues() }}</td>
                            <td>{{ $connectedOrder->bookedPaymentsSum() }}</td>
                            <td>{{ $connectedOrder->promisePaymentsSum() }}</td>
                            <td>{{ $connectedOrder->toPayPackages() }}</td>
				<td>
                                @foreach($connectedOrder->packages as $package)
                                    @if($package->status == 'CANCELLED' || $package->status == 'WAITING_FOR_CANCELLED')

                                    @else
                                        <p style="display: inline-block;">{{$package->cash_on_delivery }}</p>
					@if($package->status == 'SENDING' || $package->status == 'DELIVERED')
                                @else
                                 <a type="button" class="open" style="text-decoration: none;display: inline-block;" data-package-id="{{ $package->id }}" data-package-value="{{ $package->cash_on_delivery }}">
                                    Zmień
                                </a>
                                @endif
                                <br/>
                                    @endif
                                @endforeach
                            </td>
				<td></td>
			<td></td>
		<td style="vertical-align: top;">
                            @foreach($order->packages as $package)
                                @if($package->status == 'CANCELLED' || $package->status == 'WAITING_FOR_CANCELLED')

                                @else
                                    <p>{{$package->letter_number}}</p>

                                @endif
                            @endforeach
                        </td>

				@php
					$sumOfGrossValues += $connectedOrder->getSumOfGrossValues();
					$bookedPaymentsSum += $connectedOrder->bookedPaymentsSum();
					$promisePaymentsSum += $connectedOrder->promisePaymentsSum();
					$toPaySum += $connectedOrder->toPayPackages();
				@endphp
                            <td style="vertical-align: top;">
                                @foreach($connectedOrder->packages as $package)
					@if($package->status == 'CANCELLED' || $package->status == 'WAITING_FOR_CANCELLED')

                                    @else
                                    <p>{{$package->delivery_courier_name}}</p>
					@endif
                                @endforeach
                            </td>            </tr>
                    @endforeach
                    <tr>
                        <td colspan="2">

                        </td>
			<td>
			{{ $sumOfGrossValues }}
			</td>
			<td>
			{{ $bookedPaymentsSum  }}
			</td>
                        <td>
			{{ $promisePaymentsSum }}
                        </td>
                        <td>
			{{ $toPaySum }}
                        </td>
			<td>
			</td>
			<td>
			<label for="cash_on_delivery">Wpisz kwotę pobrania na tworzonym LP</label>
                                <input type="number" step=".01" class="form-control" id="cash_on_delivery" style="border: 1px solid green;" name="cash_on_delivery"
                                       value="{{ $toPaySum }}">
			Automatycznie wpisana kwota jest kwotą sugerowaną, która zamyka bilans wszystkich zleceń (głównego i połączonych)
			</td>
                        
			<td>
			<h3><span id="packageCost"></span> zł <input type="hidden" value="" id="toCheck" name="toCheck"></h3>
                        </td>
                    </tr>
                    </tbody>
                </table>
            <div class="text-right">
                <h3 style="display: none;">Bilans zamówień: {{ $allOrdersSum }} zł</h3>
                <h3 style="display: none;">Pobrane łącznie w LP: {{ number_format(($allOrdersSum) - ($allOrdersSum - $cashOnDeliverySum), 2) }} zł</h3>
            </div>

            <div class="form-group">
                <label for="notices">@lang('order_packages.form.notices')</label>
                <textarea cols="40" rows="5" type="text" class="form-control" id="notices" name="notices">
                       {{ old('notices') }}</textarea>
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
                <label for="sending_number">@lang('order_packages.form.sending_number')</label>
                <input type="text" class="form-control" id="sending_number" name="sending_number"
                       value="{{ old('sending_number') }}">
            </div>
            <div class="form-group">
                <label for="letter_number">@lang('order_packages.form.letter_number')</label>
                <input type="text" class="form-control" id="letter_number" name="letter_number"
                       value="{{ old('letter_number') }}">
            </div>
            <div class="form-group">
                <label for="cost_for_client">@lang('order_packages.form.cost_for_client')</label>
                <input type="number" step=".01" class="form-control" id="cost_for_client" name="cost_for_client"
                       value="{{ old('cost_for_client') }}">
            </div>
            <div class="form-group">
                <label for="cost_for_company">@lang('order_packages.form.cost_for_company')</label>
                <input type="number" step=".01" class="form-control" id="cost_for_company" name="cost_for_company"
                       value="{{ old('cost_for_company') }}">
            </div>
            <div class="form-group">
                <label for="real_cost_for_company">@lang('order_packages.form.real_cost_for_company')</label>
                <input type="number" step=".01" class="form-control" id="real_cost_for_company"
                       name="real_cost_for_company"
                       value="{{ old('real_cost_for_company') }}">
            </div>
            <input type="hidden" value="{{ $id }}" name="order_id">
            <input type="hidden" id="chosen_data_template" value="" name="chosen_data_template">
            <input type="hidden" name="shouldTakePayment" value="0" id="shouldTakePayment">
        </div>
        <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
    </form>
    <form action="" id="cashon">
    </form>
    <div class="modal fade" id="packageDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ action('OrdersPackagesController@changeValue') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Zmiana wartości przesyłki <span
                                    class="package_id"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cash_on_delivery">@lang('order_packages.form.cash_on_delivery')</label>
                            <input type="number" step=".01" class="form-control" id="modalPackageValue"
                                   name="modalPackageValue"
                                   value="0">
                            <input type="hidden" value="0" name="packageId" id="packageId">
                            <input type="hidden" value="0" id="template-id" name="template-id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Zapisz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
      let templateData = @json($templateData);
      let orderData = @json($orderData);
      let payments = @json($payments);
      let promisedPayments = @json($promisedPayments);
      let paymentsSum = 0;
      let promisedPaymentsSum = 0;

        $(document).ready(function() {
             let toPay = {{ $toPaySum }};
             let templateId = '{{ Session::get('template-id') }}';
             $('#packageCost').text(toPay - $('#cash_on_delivery').val());
             $('#toCheck').val(toPay - $('#cash_on_delivery').val());


             $('#cash_on_delivery').on('change', function() {
                 let difference = toPay - $(this).val();
                 $('#toCheck').val(toPay - $('#cash_on_delivery').val());
                 if(difference != 0) {
                     $('#packageCost').addClass('wrong-difference');
                     $('#packageCost').text(difference);
                 } else {
                     $('#packageCost').removeClass('wrong-difference');
                     $('#packageCost').text(difference);
                 }

             });

            $('#data_template').on('change', function() {
                $('#template-id').val($(this).val());
            });
            if(templateId != '') {
                $('#data_template').val(templateId).trigger('change');
            }



        });


      payments.forEach(function (payment) {
        paymentsSum = payment.amount;
      });

      promisedPayments.forEach(function (payment) {
        promisedPaymentsSum = payment.amount;
      });


      $('#data_template').change(function () {
        let selectedTemplateData = templateData[$("#data_template option:selected").val()];

        $("#chosen_data_template").val(selectedTemplateData['name']);

        $("#size_a").val(selectedTemplateData['size_a']);
        $("#size_b").val(selectedTemplateData['size_b']);
        $("#size_c").val(selectedTemplateData['size_c']);
        $("#service_courier_name").val(selectedTemplateData['service_courier_name']);
        $("#delivery_courier_name").val(selectedTemplateData['delivery_courier_name']);
        $("#quantity").val(selectedTemplateData['quantity']);
        $("#shape").val(selectedTemplateData['shape']);
        $("#shipment_date").val(orderData['shipment_date']);
        $("#delivery_date").val(orderData['delivery_date']);
        $("#weight").val(selectedTemplateData['weight']);
        $("#notices").val(orderData['customer_notices']);
        $("#cost_for_client").val(orderData['shipment_price_for_client']);
        $("#cost_for_company").val(orderData['shipment_price_for_us']);
        $("#container_type").val(selectedTemplateData['container_type']);
      });

      $("#shipment_date").on('dp.change', function () {
        var shipmentDate = new Date($("#shipment_date").val());
        var deliveryDate = "", noOfDaysToAdd = 1, count = 0;

        while (count < noOfDaysToAdd) {
          deliveryDate = new Date(shipmentDate.setDate(shipmentDate.getDate() + 1));
          if (deliveryDate.getDay() != 0 && deliveryDate.getDay() != 6) {
            //Date.getDay() gives weekday starting from 0(Sunday) to 6(Saturday)
            count++;
          }
        }

        $("#delivery_date").val(moment(deliveryDate).format('YYYY-MM-DD'));

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

      breadcrumb.children().remove();
      breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
      breadcrumb.append("<li class='active'><a href='/admin/orders/{{$id}}/edit'>Przesyłki</a></li>");
      breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
    </script>
@endsection
