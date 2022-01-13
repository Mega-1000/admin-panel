<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">

    <style>
        body,
        html,
        .docc {
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
        <table border="0" cellpadding="1" cellspacing="1" style="width: 60%;">
            @foreach($productsStockPositions as $productStockPosition)
            <tr>
                <td>{{ $productStockPosition->id }}</td>
                <td>{{ $productStockPosition->stock->product->name }}</td>
                <td>
                    {{ $productStockPosition->stock->product->symbol }}
                </td>
                <td>
                    Ilość wszystkich: {{ $productStockPosition->stock->quantity }} <br />

                    <p>
                        Aleja: <b>{{ $productStockPosition->lane }}</b><br>
                        Regał: <b>{{ $productStockPosition->bookstand }}</b><br>
                        Półka: <b>{{ $productStockPosition->shelf }}</b> <br>
                        Pozycja <b>{{ $productStockPosition->position }}</b> <br />
                        Ilość na pozycji: <b style="color: red;">{{ $productStockPosition->position_quantity }}</b>
                    </p>
                    JZ:
                    @if($productStockPosition->stock->product->number_of_sale_units_in_the_pack != 0)
                    {{ floor($productStockPosition->position_quantity / $productStockPosition->stock->product->number_of_sale_units_in_the_pack) }}
                    @else
                    0
                    @endif
                    <br />
                    JH:
                    @if($productStockPosition->stock->product->number_of_sale_units_in_the_pack == 0 || $productStockPosition->position_quantity
                    < 0) {{ $productStockPosition->position_quantity }} @else {{ $productStockPosition->position_quantity - (floor($productStockPosition->position_quantity / $productStockPosition->stock->product->number_of_sale_units_in_the_pack) * $productsStockPositions->stock->product->number_of_sale_units_in_the_pack) }} @endif <br /><br />
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <hr />
                </td>
            </tr>
            @endforeach
        </table>

    </div>

    <div style="page-break-before: always"></div>


    <script>
        window.print();
    </script>
    <style>
        p {
            margin: 0px;
        }
    </style>

</body>

</html>