<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularz Awizacyjny</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
        h1, h2 { color: #333; }
        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px 0; }
        .info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h1>FORMULARZ AWIZACYJNY</h1>

<p>Prosimy o zatwierdzenie lub odrzucenie awizacji:</p>

<a href="/avization-viewed/{{ $order->id }}" class="button">OZNACZ TĄ AWIZACJĘ JAKO WYŚWIETLONĄ</a>
<a href="{{ $formLink }}" class="button">FORMULARZ POTWIERDZANIA AWIZACJI</a><br>
<a href="{{$sendFormInvoice}}" class="button">TOWAR ZOSTAŁ WYDANY</a><br>
<a href="{{$sendFormInvoice}}" class="button">ZAŁĄCZ FAKTURĘ</a><br>
<a href="{{ $chatLink }}" class="button">LINK DO CHATU</a>

<div class="info">
    <h2>Fakturę prosimy wystawić na:</h2>
    <p>
        ELEKTRONICZNA PLATFORMA HANDLOWA EU SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ<br>
        ul. Norwida 31<br>
        55-200 Oława<br>
        NIP: 9121945342
    </p>
</div>

<div class="info">
    <h2>Parametry zamówienia:</h2>
    <p>Nr oferty: {{$order->id}}</p>
    <h3>Dane do dostawy:</h3>
    <p>
        {{$order->addresses->first->id->firstname}} {{$order->addresses->first->id->lastname}}<br>
        {{$order->addresses->first->id->address}} {{$order->addresses->first->id->flat_number}}<br>
        {{$order->addresses->first->id->postal_code}} {{$order->addresses->first->id->city}}<br>
        Tel: {{$order->addresses->first->id->phone}}
    </p>
</div>

<div class="info">
    <h2>Zawartość zamówienia:</h2>
    <table>
        <tr>
            <th>Nazwa produktu</th>
            <th>Symbol</th>
            <th>Ilość</th>
            <th>Cena brutto</th>
            <th>Cena netto</th>
            </tr>
            @foreach($order->items as $item)
                <tr>
                    <td>{{$item->product->name}}</td>
                    <td>{{$item->product->symbol}}</td>
                    <td>{{$item->quantity}}</td>
                    <td>{{number_format($item->net_purchase_price_commercial_unit_after_discounts * 1.23, 2)}} zł</td>
                    <td>{{round(number_format($item->net_purchase_price_commercial_unit_after_discounts, 2), 2)}} zł</td>

                </tr>
            @endforeach
    </table>
</div>

<div class="info">
    <p>Koszt transportu dla nas brutto: {{$order->shipment_price_for_us}} zł</p>
    <p>Data rozpoczęcia nadawania przesyłki: {{$order->shipment_date}}</p>
</div>

<p><strong>Uwaga:</strong> Brak odpowiedzi w ciągu 2 godzin spowoduje wysyłanie przypomnienia co 15 minut.</p>

<p>Z pozdrowieniami,<br>Zespół Ephpolska</p>
</body>
</html>
