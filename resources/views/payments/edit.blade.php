@extends('layouts.app')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-person"></i> @lang('customers.edit')
        <a style="margin-left:15px" href="{{ action('OrdersPaymentsController@payments') }}"
           class="btn btn-info install pull-right">
            <span>Lista klientów</span>
        </a>
    </h1>
@endsection

@section('app-content')
    <div class="browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="order-payments" id="order-payments">
                    @if(!empty($uri))
                        <input id="uri" type="hidden" value="{{$uri}}">
                    @endif
                    <h1>Rozrachunki z kontrahentem: {{ $customer->login }}
                        <a id="create-button-orderPayments" style="float:right;margin-right: 15px;"  href="{{ route('order_payments.createMasterWithoutOrder', ['id' => $customer->id]) }}" class="btn btn-success install pull-right">
                            <i class="voyager-plus"></i> <span>@lang('order_payments.createMaster')</span>
                        </a>
                    </h1>
                    <table style="width: 50%; float: left;" id="paymentsTable" class="table table-hover">
                        <thead>
                        <tr>
                            <th>@lang('order_payments.table.payments')</th>
                            <th>@lang('order_payments.table.booked_date')</th>
                            <th>@lang('order_payments.table.title')</th>
                            <th>@lang('order_payments.table.booked_orders')</th>
                            <th>@lang('order_payments.table.payment_left')</th>
                            <th>@lang('order_payments.table.add')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($customer->payments as $payment)
                            <tr>
                                <td>{{ $payment->amount }}</td>
                                <td>{{ $payment->created_at }}</td>
                                <td>{{ $payment->title }}</td>
                                <td>
                                    @foreach($payment->getOrdersUsingPayment() as $orderId => $paymentsValue)
                                        <b style="font-weight: 700;">{{ $orderId }}</b> - {{ $paymentsValue }} zł <br/>
                                    @endforeach
                                </td>
                                <td>{{ $payment->amount_left }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary openPaymentModal" data-payment="{{ $payment->id }}">
                                        Przydziel
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <table style="width: 49%; margin-left: 1%; display: inline-block;" id="ordersTable" class="table table-hover">
                        <thead>
                        <tr>
                            <th>@lang('order_payments.table.order_id')</th>
                            <th>@lang('order_payments.table.order_status')</th>
                            <th>@lang('order_payments.table.order_value')</th>
                            <th>@lang('order_payments.table.booked')</th>
                            <th>@lang('order_payments.table.left')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($customer->orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->status->name }}</td>
                                @php
                                    $orderValue = $order->total_price + $order->shipment_price_for_client + $order->additional_service_cost
                                @endphp
                                <td>{{ $orderValue }}</td>
                                @php
                                    $paymentsValue = 0;
                                @endphp
                                @foreach($order->payments as $payment)
                                    @php
                                        $paymentsValue += $payment->amount
                                    @endphp
                                @endforeach
                                <td>{{ $paymentsValue }}</td>
                                <td>{{ $orderValue - $paymentsValue }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModal" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Przydziel wpłatę do zamówienia</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ action('OrdersPaymentsController@store') }}" method="POST">
                                    {{ csrf_field() }}
                                    <div class="firms-general" id="orderPayment">
                                        <div class="form-group">
                                            <label for="amount">@lang('order_payments.form.amount')</label>
                                            <input type="text" class="form-control" id="amount" name="amount"
                                                   value="{{ old('amount') }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="notices">@lang('order_payments.form.notices')</label>
                                            <textarea class="form-control" id="notices" name="notices"
                                                      value="{{old('notices')}}" rows="5"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="chooseOrder">Wybierz zlecenie</label>
                                            <select class="form-control" id="chooseOrder" name="chooseOrder">
                                                @foreach($customer->orders as $order)
                                                    @php
                                                        $orderValue = $order->total_price + $order->shipment_price_for_client + $order->additional_service_cost
                                                    @endphp
                                                    <option value="{{ $order->id }}">Zlecenie: {{ $order->id }} Kwota zlecenia: {{ $orderValue }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" value="0" name="masterPaymentId">
                                        <input type="hidden" value="{{ $customer->id }}" name="customer_id">
                                    </div>
                                    <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('javascript')
    <script>

        $('.openPaymentModal').on('click', function(){
            let masterPaymentId = $(this).data('payment');
            $('input[name="masterPaymentId"]').val(masterPaymentId);
            $('#paymentModal').modal();
            console.log('xxx');
        });
    </script>
@endsection
