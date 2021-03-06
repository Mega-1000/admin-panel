<!DOCTYPE html>
<html lang="pl">
<head>
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
    <table border="0" cellpadding="1" cellspacing="1" style="width: 60%;">
        @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>
                    {{ $product->symbol }}
                </td>
                <td>
                    Ilość wszystkich: {{ $product->quantity }} <br/>
                    @foreach($product->positions as $position)
                        <p>
                            Pozycja: <b>{{ $position->lane }}</b> <b>{{ $position->bookstand }}</b> <b>{{ $position->shelf }}</b> <b>{{ $position->position }}</b> <br/>
                            Ilość na pozycji: <b style="color: red;">{{ $position->position_quantity }}</b> </p>
                            JZ:
                            @if($product->number_of_sale_units_in_the_pack != 0)
                                {{ floor($position->position_quantity / $product->number_of_sale_units_in_the_pack) }}
                            @else
                                0
                            @endif
                            <br/>
                            JH:
                            @if($product->number_of_sale_units_in_the_pack == 0 || $position->position_quantity < 0)
                                {{ $position->position_quantity }}
                            @else
                                {{ $position->position_quantity - (floor($position->position_quantity / $product->number_of_sale_units_in_the_pack) * $product->number_of_sale_units_in_the_pack) }}
                            @endif
                            <br/><br/>
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
