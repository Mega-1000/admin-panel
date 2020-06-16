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

    </style>
</head>
<body>


<div class="docc">
    <h1>WZ {{ $order->id }}</h1>
    @if(!empty($order->employee))
        <h2>Osoba odpowiedzialna:</h2> {!! $tagHelper->consultantOrStorekeeper() !!}<br/>
    @endif
    <div style="position:absolute; right:40px; top:0px;">
        <h2 style="display: inline-block">
            MEGA 1000 BIS SP Z O O<br/>
            ZEROMSKIEGO 52/18<br/>
            50-312 WROCLAW<br/>
            NIP: 8971719229
        </h2>
    </div>

    Waga zamowienia: {{ $order->weight }}<br/><br/>

    <b>ZALICZKA - ZAKSIEGOWANA:</b> {{ $order->payments->sum('amount') }}<br/>
    <b>Data nadania przesylki:</b> {{ $tagHelper->cargoSentDate() }}<br/>

    {{--<b>uwagi klienta:</b> <br/>--}}
    <b>uwagi spedycja:</b> {!! $tagHelper->commentsToShipping(" | ") !!}<br/>
    <b>uwagi magazyn:</b> {!! $tagHelper->commentsToStorehouse(" | ") !!}

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
            @if(count($package->packedProducts) > 0)
                @foreach($package->packedProducts as $item)
                    <table border="0" cellpadding="1" cellspacing="1" style="width: 100%;">
                        <tr>
                            <td style="width:100px"><img
                                    src="{!! $item->getImageUrl() !!}"
                                    alt="{{ $item->name }}"
                                    style="width:70px"/></td>
                            <td><span
                                    style="font-size:14px; font-weight:bold;">{{ $item->name }}</span><br/>symbol: {{ $item->symbol }}
                                <br/>Ilość: {{ $item->pivot->quantity }} {{ $item->packing->calciation_unit }}<br/></td>
                            @if($showPosition)
                                <td>
                                    ILOŚĆ NA STANIE: {{ $item->stock->quantity }} <br/>
                                    @if(count($item->getPositions()))
                                        LOKACJA PRODUKTÓW: <br/>
                                        @foreach($item->getPositions() as $position)
                                            Alejka: {{ $position->lane }} <br/>
                                            Regał: {{ $position->bookstand }} </br>
                                            Półka: {{ $position->shelf }} </br>
                                            Pozycja: {{ $position->position }} </br>
                                            Ilość: {{ $position->position_quantity }}
                                        @endforeach
                                    @endif
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td colspan="3">
                                <hr/>
                            </td>
                        </tr>
                    </table>
                @endforeach
            @endif
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
            @include('orders.items_table',['$package' => $package])
        @endforeach
    @endif
    @if(count($order->notCalculable) > 0)
        @foreach($order->notCalculable as $package)
            <table border="1" cellpadding="2" cellspacing="0" style="width: 100%;">
                <tr style="background: red">
                    <th>Produkty niepoliczalne</th>
                </tr>
            </table>
            <br/><br/>
            @include('orders.items_table',['$package' => $package])
        @endforeach
    @endif


    <br/><b>DANE KUPUJĄCEGO</b><br/>
    {!! $tagHelper->buyerData() !!}


    <br><br/><b>DANE DO DOSTAWY</b><br/>
    {!! $tagHelper->shipmentData() !!}
</div>

<div style="page-break-before: always"></div>


<script>
    window.print();
</script>

</body>
</html>
