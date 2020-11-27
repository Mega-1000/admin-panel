<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans !important;
            font-size: 9px !important;
        }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div>
    <div class="title"><h2>Protokól odbioru paczek {{$date}} dla firmy {{$courierName}} </h2></div>
    <table class="table table-striped">
        <thead>
        <tr style="height:20px;">
            <th>Lp.</th>
            <th>Numer zlecenia/numer paczki.</th>
            <th>Numer LP</th>
            <th>Magazyn</th>
            <th>Maksymalny rozmiar paczki</th>
            <th>Firma sped.</th>
            <th>Ilosc</th>
            <th>Maksymalna waga paczki</th>
            <th style="text-align: center;">Opak</th>
            <th>Telefon</th>
            <th>Kod pocztowy</th>
            <th>Miasto</th>
        </tr>
        </thead>
        <tbody style="padding: 0px !important; margin: 0px !important;">
        @php
            $i = 0;
        @endphp
        @foreach($packages as $package)
            @if($package['letter_number'] !== null)
                {{$i++}}
                <tr style="padding: 0px !important; margin: 0px !important; height: 15px !important;">
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important;">{{$i}}</td>
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important;">{{$package['order_id']}}/{{$package['number']}}</td>
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important;">{{$package['letter_number']}}</td>
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important; font-size: 8px !important;">{{$package['warehouse']}}</td>
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important; text-align: center;">{{$package['size_a']}}x{{$package['size_b']}}x{{$package['size_c']}}</td>
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important; text-align: center;">{{$package['delivery_courier_name']}}</td>
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important; text-align: center;">{{$package['quantity']}}</td>
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important; text-align: center;">{{$package['weight']}}</td>
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important; text-align: center;">{{$package['container_type']}}</td>
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important; text-align: center;">{{$package['phone']}}</td>
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important; text-align: center;">{{$package['postal_code']}}</td>
                    <td style="padding: 0px !important; margin: 0px !important; height: 15px !important; text-align: center;">{{$package['city']}}</td>
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
            <th style="text-align: center;">
                <span>..........................</span><br>
                <span style="font-size: 6px;">podpis</span>
            </th>
            <th></th>
        </tr>
        </tfoot>
    </table>
</div>


</body>
</html>
