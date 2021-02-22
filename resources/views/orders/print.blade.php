<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Zamówienie {{ $order->id }}</title>
    {{--    <meta charset="UTF-8">--}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        body, html, .docc {
            background-color: #fff !important;
            padding: 10px;
            background: transparent;
            position: relative;
            font: 9px Arial, Helvetica, sans-serif;
        }

        h2 {
            padding-top: 20px;
            font: 10px Arial, Helvetica, sans-serif;
        }

        .quantity {
            font-size: 1.5em;
            font-weight: bold;
        }

        .wz__image {
            width: 100px;
        }
    </style>
</head>
<body>


<div class="docc">
    <h1>WZ {{ $order->id }} {!! $similar && $showPosition ? '(D) ' . join(', (D) ',$similar) : '' !!}</h1>
    @if(!empty($order->employee))
        <h2>Osoba odpowiedzialna:</h2> {!! $tagHelper->consultantOrStorekeeper() !!}<br/>
    @endif
    <div style="position:absolute; right:40px; top:0px;">
        <h2 style="display: inline-block">
            ELEKTRONICZNA PLATFORMA HANDLOWA<br/>
            UL. IWASZKIEWICZA 15A<br/>
            55-200 OŁAWA<br/>
            NIP: 9121027907
        </h2>
    </div>

    Waga zamowienia: {{ $order->weight }}<br/><br/>

    <b>Data nadania przesylki:</b> {{ $tagHelper->cargoSentDate() }}<br/>

    {{--<b>uwagi klienta:</b> <br/>--}}
    <b>uwagi spedycja:</b> {!! $tagHelper->commentsToShipping(" | ") !!}<br/>
    <b>uwagi magazyn:</b> {!! $order->warehouse_notice !!}
    <br/><br/>

    @if(count($order->packages) > 0)
        @foreach($order->packages as $package)
            <table border="1" cellpadding="2" cellspacing="0" style="width: 100%;">
                <tr style="background: green">
                    <th>Data wysyłki</th>
                    <th>Kurier</th>
                    <th>Spedycja obsługująca</th>
                    <th>Rozmiar</th>
                    <th>Waga</th>
                    <th>Uwagi</th>
                </tr>
                <tr>
                    <td>{{ $package->shipment_date }}</td>
                    <td>{{ $package->delivery_courier_name }}</td>
                    <td>{{ $package->service_courier_name }}</td>
                    <td>
                        {{ $package->size_a }} x {{ $package->size_b }} x {{ $package->size_c }}
                    </td>
                    <td>{{ $package->weight }}</td>
                    <td>{{ $order->id }}/{{ $package->number }}</td>
                </tr>
            </table>
            <br/><br/>
        @endforeach
    @endif
    @if(count($order->factoryDelivery) > 0)
        @foreach($order->factoryDelivery as $package)
            <table border="1" cellpadding="2" cellspacing="0" style="width: 100%;">
                <tr style="background: green">
                    <th>Fabryka:</th>
                    <th>Cena:</th>
                </tr>
                <tr>
                    <td>{{ $package->description }}</td>
                    <td>{{ $package->price }}</td>
                </tr>
            </table>
            <br/><br/>
        @endforeach
    @endif
    <table border="1" cellpadding="2" cellspacing="0" style="width: 100%;">
        <tr style="background: red">
            <th>Produkty niepoliczalne</th>
        </tr>
    </table>
    <br/><br/>
    @include('orders.items_table',['items' => $order->items])

    <br/><b>DANE KUPUJĄCEGO</b><br/>
    {!! $tagHelper->buyerData() !!}


    <br><br/><b>DANE DO DOSTAWY</b><br/>
    {!! $tagHelper->shipmentData() !!}
</div>

@if(empty($notPrint))
    <script>
        window.print();
    </script>
@endif

</body>
</html>
