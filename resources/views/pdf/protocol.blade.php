<html>
<head>
    <style>
        body {
            font-family: Verdana !important;
            font-size: 12px !important;
        }
    </style>
    <meta charset="UTF-8">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div>
    <div class="title"><h2>Protokól odbioru paczek z dnia {{$date}} dla firmy {{$courierName}} </h2></div>
    <table class="table table-striped">
        <thead>
        <tr style="height:20px;">
            <th>Lp.</th>
            <th>Numer zlecenia/numer paczki.</th>
            <th>Numer LP</th>
            <th>Magazyn</th>
            <th>Rozmiar</th>
            <th>Firma sped.</th>
            <th>Ilosc</th>
            <th>Waga</th>
            <th>Opak</th>
            <th>Telefon</th>
            <th>Kod pocztowy</th>
            <th>Miasto</th>
        </tr>
        </thead>
        <tbody>
        @php
            $i = 0;
        @endphp
        @foreach($packages as $package)
            {{$i++}}
            <tr>
                <td>{{$i}}</td>
                <td>{{$package['order_id']}}/{{$package['number']}}</td>
                <td>{{$package['letter_number']}}</td>
                <td>{{$package['warehouse']}}</td>
                <td>{{$package['size_a']}}x{{$package['size_b']}}x{{$package['size_c']}}</td>
                <td>{{$package['delivery_courier_name']}}</td>
                <td>{{$package['quantity']}}</td>
                <td>{{$package['weight']}}</td>
                <td>{{$package['container_type']}}</td>
                <td>{{$package['phone']}}</td>
                <td>{{$package['postal_code']}}</td>
                <td>{{$package['city']}}</td>
            </tr>
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
