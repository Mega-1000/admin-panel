<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Zamówienie {{ $order->id }}</title>
    <meta charset="UTF-8">

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
        <h2>
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
    <table border="1" cellpadding="2" cellspacing="0" style="width: 100%;">
        <tr>
            <th>Data wysyłki</th>
            <th>Kurier</th>
            <th>Spedycja obsługująca</th>
            <th>Rozmiar</th>
            <th>Waga</th>
            <th>Uwagi</th>
        </tr>
        @foreach($order->packages as $package)
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
        @endforeach
    </table>
    <br/><br/>
    @endif
    <table border="0" cellpadding="1" cellspacing="1" style="width: 100%;">
        @foreach($order->items as $item)
            <tr>
                <td style="width:100px"><img
                        src="{!! str_replace('C:\\z\\', env('APP_URL') . 'storage/', $item->product->url) !!}"
                        alt="{{ $item->product->name }}"
                        style="width:70px"/></td>
                <td><span
                        style="font-size:14px; font-weight:bold;">{{ $item->product->name }}</span><br/>symbol: {{ $item->product->symbol }}
                    <br/>Ilość: {{ $item->quantity }} {{ $item->product->packing->calciation_unit }}<br/></td>
                <td>
                    ILOSC NA STANIE: {{ $item->realProduct() }} <br/>
                    @if(count($item->realProductPositions()))
                    LOKACJA PRODUKTOW: <br/>
                        @foreach($item->realProductPositions() as $position)
                            Alejka: {{ $position->lane }} <br/>
                            Regał: {{ $position->bookstand }} </br>
                            Półka: {{ $position->shelf }} </br>
                            Pozycja: {{ $position->position }} </br>
                            Ilość: {{ $position->position_quantity }}
                        @endforeach
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <hr/>
                </td>
            </tr>
        @endforeach
    </table>


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
