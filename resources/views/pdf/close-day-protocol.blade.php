<html>
<head>
    <style>
        body {
            font-family: Times-Roman, sans-serif !important;
            font-size: 9px !important;
        }

        tbody {
            padding: 0px !important;
            margin: 0px !important;
        }

        tbody tr {
            padding: 0px !important;
            margin: 0px !important;
            height: 15px !important;
        }

        tbody tr td {
            padding: 0px !important;
            margin: 0px !important;
            height: 15px !important;
        }

        .warehouse {
            font-size: 8px !important;
        }

        .text-center {
            text-align: center !important;
        }

        .signature {
            font-size: 6px !important;
        }

        thead tr {
            height: 20px;
        }

        .page-break {
            page-break-after: always;
        }

        input[type=checkbox] {
            border: 1px solid slategrey;
            background-color: white;
            height: 15px;
            width: 15px;
            margin-left: 45px
        }

        big-text {
            font-size: 72px;
        }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div>
    <div class="title"><h2>Protokól zamkniecia dnia wysylkowego {{$date->toDateString()}} po odbiorze paczek
            przez {{$courierName}} </h2></div>
    <p>Dla ponizszych paczek data wysylki zostala przesunieta na kolejny dzien roboczy</p>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Lp.</th>
            <th>Numer zlecenia/numer paczki.</th>
            <th>Numer LP</th>
            <th>Magazyn</th>
            <th>Maksymalny rozmiar paczki</th>
            <th>Firma sped.</th>
            <th>Ilosc</th>
            <th>Maksymalna waga paczki</th>
            <th class="text-center">Opak</th>
            <th>Przeniesiony na:</th>
            <th class="text-center">Przeniesione na kolejny dzien</th>
        </tr>
        </thead>
        <tbody>
        @php
            $i = 0;
        @endphp
        @foreach($packages as $package)
            @if($package['letter_number'] !== null)
                {{$i++}}
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$package['order_id']}}/{{$package['number']}}</td>
                    <td>{{$package['letter_number']}}</td>
                    <td class="warehouse">{{$package['warehouse']}}</td>
                    <td>{{$package['size_a']}}x{{$package['size_b']}}x{{$package['size_c']}}</td>
                    <td>{{$package['delivery_courier_name']}}</td>
                    <td>{{$package['quantity']}}</td>
                    <td>{{$package['weight']}}</td>
                    <td>{{$package['container_type']}}</td>
                    <td>{{ $package['shipment_date']->toDateString() }}</td>
                    <td class="text-center"><input type="checkbox" name="check"/></td>
                </tr>
            @endif
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th>Liczba przesylek: {{$i}}</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class="text-center">
                <span>..........................</span><br>
                <span class="signature">podpis</span>
            </th>
            <th></th>
        </tr>
        </tfoot>
    </table>
</div>

<div class="page-break"></div>

<div>
    <h1 style=" display: inline-block; font-size:48px !important; text-align: center; margin-top: 200px; width: 100%;">
        Wysylka na dzien {{ $date->toDateString() }} zostala zamknieta. Kolejne paczki zostana nadane w nastepnym
        dniu roboczym</h1>
</div>

<div class="page-break"></div>

<div>
    <h1 style=" display: inline-block; font-size:48px !important; text-align: center; margin-top: 200px; width: 100%;">
        Paczki na dzien {{ $date->addWeekday()->toDateString() }}.</h1>
</div>
</body>
</html>
