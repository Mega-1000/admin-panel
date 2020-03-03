<html>
<head>
    <style>
        *{ font-family: DejaVu Sans !important;}
        p {
            font-size: 0.8em;
            margin: 0;
        }
        h4 {
            font-size: 1em;
        }
        tfoot {
            font-size: 0.7em;
        }
        thead {
            font-size: 0.7em;
        }
        tbody {
            font-size: 0.8em;
        }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div>
    <table style="table-layout:fixed; width: 100%;">
        <tbody>
            <tr>
                <td style="width: 50%">
                    <h4>Proforma PRO/{{$order->id}}/{{ $proformDate }}</h4>
                    Data wystawienia: {{ $date }} <br/>
                    Data sprzedaży: {{ $date }}
                </td>
            </tr>
            <tr>
                <td>
                    <h4>Sprzedawca</h4>
                    <p> MEGA 1000 BIS SP Z O O <br/>
                        ŻEROMSKIEGO 52/18    <br/>
                        50-312 WROCŁAW <br/>
                        NIP: 8971719229 <br/>
                        Nr konta: {{env('ACCOUNT_PROFORMA')}} <br/>
                        Telefony: {{env('FRONT_URL')}}/kontakt <br/>
                        E-mail ogólny : {{'info@' . env('DOMAIN_NAME')}} <br/>
                    </p>
                </td>
                <td>
                    <h4>Nabywca</h4>
                    @if($order->isInvoiceDataComplete())
                    <p> {{ $order->getInvoiceAddress()->firstname }} {{ $order->getInvoiceAddress()->lastname }} {{ $order->getInvoiceAddress()->firmname }} <br/>
                        {{ $order->getInvoiceAddress()->address }} {{ $order->getInvoiceAddress()->flat_number }}    <br/>
                        {{ $order->getInvoiceAddress()->postal_code }} {{ $order->getInvoiceAddress()->city }}<br/>
                        @if($order->getInvoiceAddress()->nip != null)
                        NIP: {{ $order->getInvoiceAddress()->nip }} <br/>
                        @endif
                    </p>
                    @else
                        <p> {{ $order->customer->standardAddress()->email }}<br/>
                            {{ $order->customer->standardAddress()->phone }}
                        </p>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table table-striped" style="table-layout:fixed; width: 100%;">
        <thead>
        <tr style="height:20px;">
            <th style="width: 3.6%">Lp.</th>
            <th style="width: 24.6%">Nazwa</th>
            <th style="width: 4.6%">Ilość</th>
            <th style="width: 10.1%">Cena netto</th>
            <th style="width: 7%;">VAT</th>
            <th style="width: 8%">Cena brutto</th>
            <th style="width: 13.1%">Wartość netto</th>
            <th style="width: 13%;">Kwota VAT</th>
            <th style="width: 13.1%">Wartość brutto</th>
        </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
                $netSum = 0;
                $grossSum = 0;
                $vatSum = 0;
            @endphp
            @foreach($order->items as $item)
                <tr>
                    <td style="width: 3.6%">{{ $i }}</td>
                    <td style="width: 24.6%">{{ $item->product->name }}</td>
                    <td style="width: 4.6%">{{ $item->quantity }}</td>
                    <td style="width: 10.1%">{{ $item->net_selling_price_commercial_unit }} zł</td>
                    <td style="width: 7%;">23%</td>
                    <td style="width: 8%">{{ number_format($item->net_selling_price_commercial_unit * 1.23, 2) }} zł</td>
                    <td style="width: 13.1%">{{ $item->net_selling_price_commercial_unit * $item->quantity }} zł</td>
                    <td style="width: 13%;">{{ number_format($item->net_selling_price_commercial_unit * 0.23 * $item->quantity, 2) }} zł</td>
                    <td style="width: 13.1%">{{ number_format($item->net_selling_price_commercial_unit * 1.23 * $item->quantity, 2) }} zł</td>
                </tr>
                @php
                    $netSum += $item->net_selling_price_commercial_unit * $item->quantity;
                    $grossSum += $item->net_selling_price_commercial_unit * $item->quantity * 1.23;
                    $vatSum += $item->net_selling_price_commercial_unit * $item->quantity * 0.23;
                    $i++;
                @endphp
            @endforeach
            @if($order->shipment_price_for_client != null && $order->shipment_price_for_client != 0.00)
                <tr>
                    <td></td>
                    <td>Koszt transportu:</td>
                    <td>1</td>
                    <td>{{ number_format($order->shipment_price_for_client/1.23, 2) }} zł</td>
                    <td>23%</td>
                    <td>{{ number_format($order->shipment_price_for_client, 2) }} zł</td>
                    <td>{{ number_format($order->shipment_price_for_client/1.23, 2) }}</td>
                    <td>{{ number_format(($order->shipment_price_for_client * 0.23)/1.23, 2) }} zł</td>
                    <td>{{ number_format($order->shipment_price_for_client, 2) }} zł</td>
                </tr>
                @php
                    $netSum += $order->shipment_price_for_client/1.23;
                    $grossSum += $order->shipment_price_for_client;
                    $vatSum += ($order->shipment_price_for_client * 0.23)/1.23;
                @endphp
            @endif
            @if($order->additional_service_cost != null && $order->additional_service_cost != 0.00)
                <tr>
                    <td></td>
                    <td>Dodatkowy koszt obsługi:</td>
                    <td>1</td>
                    <td>{{ number_format($order->additional_service_cost/1.23, 2) }} zł</td>
                    <td>23%</td>
                    <td>{{ number_format($order->additional_service_cost, 2) }} zł</td>
                    <td>{{ number_format($order->additional_service_cost/1.23, 2) }}</td>
                    <td>{{ number_format(($order->additional_service_cost * 0.23)/1.23, 2) }} zł</td>
                    <td>{{ number_format($order->additional_service_cost, 2) }} zł</td>
                </tr>
                @php
                    $netSum += $order->additional_service_cost/1.23;
                    $grossSum += $order->additional_service_cost;
                    $vatSum += ($order->additional_service_cost * 0.23)/1.23;
                @endphp
            @endif
            @if($order->additional_cash_on_delivery_cost != null && $order->additional_cash_on_delivery_cost != 0.00)
                <tr>
                    <td></td>
                    <td>Dodatkowy koszt pobrania:</td>
                    <td>1</td>
                    <td>{{ number_format($order->additional_cash_on_delivery_cost/1.23, 2) }} zł</td>
                    <td>23%</td>
                    <td>{{ number_format($order->additional_cash_on_delivery_cost, 2) }} zł</td>
                    <td>{{ number_format($order->additional_cash_on_delivery_cost/1.23, 2) }}</td>
                    <td>{{ number_format(($order->additional_cash_on_delivery_cost * 0.23)/1.23, 2) }} zł</td>
                    <td>{{ number_format($order->additional_cash_on_delivery_cost, 2) }} zł</td>
                </tr>
                @php
                    $netSum += $order->additional_cash_on_delivery_cost/1.23;
                    $grossSum += $order->additional_cash_on_delivery_cost;
                    $vatSum += ($order->additional_cash_on_delivery_cost * 0.23)/1.23;
                @endphp
            @endif
        </tbody>
        <tfoot>
        <tr style="background-color: grey;">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Suma</th>
            <th>{{ number_format($netSum, 2) }} zł</th>
            <th>{{ number_format($vatSum, 2) }} zł</th>
            <th>{{ number_format($grossSum, 2) }} zł</th>
        </tr>


        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th colspan="2">Razem do zapłaty: {{ number_format($order->getSumOfGrossValues(), 2) }} zł</th>
        </tr>

        </tfoot>
    </table>
</div>


</body>
</html>
