<body>
<style>
    table.minimalistBlack {
        border: 3px solid #000000;
        margin-top: 200px;
        width: 100%;
        height: auto;
        text-align: left;
        border-collapse: collapse;
        font-family: Verdana,Geneva,sans-serif;
    }

    table.minimalistBlack td, table.minimalistBlack th {
        border: 1px solid #8E8E8E;
        padding: 5px 4px;
    }

    table.minimalistBlack tbody td {
        font-size: 13px;
    }

    table.minimalistBlack tr:nth-child(even) {
        background: #FFFFFF;
    }

    table.minimalistBlack thead {
        background: #CFCFCF;
        background: -moz-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
        background: -webkit-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
        background: linear-gradient(to bottom, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
        border-bottom: 3px solid #000000;
    }

    table.minimalistBlack thead th {
        font-size: 15px;
        font-weight: bold;
        color: #000000;
        text-align: left;
    }

    table.minimalistBlack tfoot {
        font-size: 14px;
        font-weight: bold;
        color: #000000;
        border-top: 3px solid #000000;
    }

    table.minimalistBlack tfoot td {
        font-size: 14px;
    }

    table tbody tr td {
        height: 125px;
    }
</style>
<table class="minimalistBlack">
    <thead>
    <tr>
        <th colspan="5">ETYKIETA ADRESOWA ZAMÓWIENIA NR: {{$order->id}}/{{$package['number']}}</th>
    </tr>
    </thead>
    <tbody>
    <tr style="background: #AABBC9;">
        <td colspan="2"><strong>Numer nalepki:</strong> {{$package['letter_number']}}</td>
        <td>
            <strong>Typ:</strong> {{$package['delivery_courier_name']}}
            <strong>Opakowanie:</strong> {{$package['container_type']}}
        </td>
        <td>
            <strong>Dlugosc:</strong> {{$package['size_a']}} cm<br>
            <strong>Szerokosc:</strong> {{$package['size_b']}} cm<br>
            <strong>Wysokosc:</strong> {{$package['size_c']}} cm<br>
        </td>
        <td><strong>Waga:</strong> {{$package['weight']}}</td>
    </tr>
    <tr>
        <td><strong>Pobranie: </strong> {{$package['cost_for_client']}}</td>
        <td colspan="2"><strong>Zawartosc: </strong><br> Materialy budowalane</td>
        <td><strong>Data wysylki: </strong>  {{$package['shipment_date']}}</td>
        <td><strong>Data dostarczenia: </strong>  {{$package['delivery_date']}}</td>
    </tr>
    <tr style="background: #AABBC9;">
        <td colspan="2">
            <strong>Odbiorca:</strong> <br>
            {{$order->addresses->first->id->firstname}} {{$order->addresses->first->id->lastname}} <br>
            Ul: {{$order->addresses->first->id->address}} {{$order->addresses->first->id->flat_number}}<br>
            {{$order->addresses->first->id->postal_code}} {{$order->addresses->first->id->city}}<br>
            Email: {{$order->addresses->first->id->email}} Nr. tel: {{$order->addresses->first->id->phone}}<br>
        </td>
        <td><strong>Uwagi:</strong><br>
            {{$package['notices']}}</td>
        <td colspan="2">
            <strong>Nadawca: </strong><br>
            @if($order->warehouse !== null)
                {{$order->warehouse->firm->name}}<br>
                Ul: {{$order->warehouse->firm->address->address}} {{$order->warehouse->firm->address->warehouse_number}}<br>
                {{$order->warehouse->firm->address->postal_code}} {{$order->warehouse->firm->address->city}}<br>
                Email: {{$order->warehouse->firm->email}} Nr. tel: {{$order->warehouse->firm->phone}}<br>
            @endif
        </td>
    </tr>
    </tbody>
</table>
</body>