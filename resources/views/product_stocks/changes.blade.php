<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">

    <style>
        body, html, .docc {
            background-color: #fff !important;
            background: transparent;
            position: relative;
            font: 9px Arial, Helvetica, sans-serif;
        }
        table {
            width: 70%;
        }
    </style>
</head>
<body>


<div class="docc">
    <h2>Wydruk produktów ze zmienionymi stanami dla przedziału dni: {{ $startDate }} - {{ $endDate }}</h2>
    <table border="0" cellpadding="1" cellspacing="1">
        <thead>
        <th>Nazwa produktu</th>
        <th>Pozycje</th>
        <th>Ilość na stanie</th>
        <th>Zlecenia</th>
        </thead>
        @foreach($groupedProductsStocksChanges as $groupedProductsStocksChange)
            <tr>
                <td>
                    {{ $groupedProductsStocksChange[0]->stock->product->name }}<br>
                    <b>Symbol produktu: {{ $groupedProductsStocksChange[0]->stock->product->symbol }}</b>
                </td>
                    <td>
                        @foreach($groupedProductsStocksChange[0]->stock->position as $position)
                            <div class="position">
                                Alejka: {{ $position->lane }} <br/>
                                Regał: {{ $position->bookstand }} </br>
                                Półka: {{ $position->shelf }} </br>
                                Pozycja: {{ $position->position }} </br>
                                Ilość: {{ $position->position_quantity }} </br>
                            </div>
                        @endforeach
                    </td>
                <td>
                    <span class="product__quantity">{{ $groupedProductsStocksChange[0]->stock->quantity }}</span>
                </td>
                <td>
                    @foreach($groupedProductsStocksChange as $stockChangeOrder)
                        @if($stockChangeOrder->order_id !== null)
                            <br/>
                            <span>Zlecenie: <a href="{{ route('orders.edit', ['id' => $stockChangeOrder->order_id]) }}">{{ $stockChangeOrder->order_id }}</a> - {{ $stockChangeOrder->quantity }} sztuk</span>
                        @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <hr/>
                </td>
            </tr>
        @endforeach
    </table>

</div>

<script>
    window.print();
</script>
<style>
    table thead th {
        text-align: left;
        padding-bottom: 10px;
    }
    .product__quantity {
        font-size: 1.5em;
        font-weight: bold;
        color: red;
    }
    .position {
        display: inline-block;
    }
</style>
</body>
</html>
