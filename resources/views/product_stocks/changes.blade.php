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
    </style>
</head>
<body>


<div class="docc">
    <h2>Wydruk produktów ze zmienionymi stanami dla przedziału dni: {{ $startDate }} - {{ $endDate }}</h2>
    <table border="0" cellpadding="1" cellspacing="1" style="width: 60%;">
        <thead>
            <th>Nazwa produktu</th>
            <th>Symbol produktu</th>
            <th>Ilość na stanie</th>
        </thead>
        @foreach($productsStocksChanges as $productsStocksChange)
            <tr>
                <td>{{ $productsStocksChange->stock->product->name }}</td>
                <td>
                    {{ $productsStocksChange->stock->product->symbol }}
                </td>
                <td>
                    <span class="product__quantity">{{ $productsStocksChange->stock->quantity }}</span>
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
</style>
</body>
</html>
