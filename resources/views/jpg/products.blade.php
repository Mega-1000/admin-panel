<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        table {
            width: 100%;
            color: #0069c0;
        }

        body {
            font-family: DejaVu Sans;
        }

        * {
            box-sizing: border-box;
        }

        td {
            padding: 10px;
            border: 2px dotted #333;
            height: 200px;
            width: 50%;
        }

        img {
            width: 120px;
        }

        .right {
            float: right;
            width: 50%;
        }

        .info {
            font-size: 24px;
            text-align: center;
        }

        .title {
            font-size: 16px;
        }

        .price {
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<table cellpadding="0" cellspacing="0" align="right">
    <tr>
        <td colspan="2" class="info">Na innych naszych aukcjach można zakupić poniższe towary:</td>
    </tr>
    @php ($i = 0)
    @foreach ($rows as $row => $rowCols)
        @foreach ($rowCols as $subcols)
            @foreach ($subcols as $data)
                @if (!file_exists(public_path($data['image'])))
                    @continue
                @endif
                {!! $i % 2 == 0 ? '<tr>' : '' !!}
                <td>
                    <img src="{{ public_path($data['image']) }}">
                    <div class="right">
                        <div style="height: 80px">
                            <p class="title">{{ $data['name'] }}</p>
                        </div>
                        <div style="height: 80px">
                            <p style="position: relative; bottom: 0" class="price">{{ $data['price'] }} zł</p>
                        </div>
                    </div>
                </td>
                {!! $i % 2 == 1 ? '</tr>' : '' !!}
                @php ($i++)
            @endforeach
        @endforeach
    @endforeach
    {!! $i % 2 == 1 && $i > 1 ? '<td></td>' : '' !!}
    {!! $i % 2 == 1 ? '</tr>' : '' !!}
</table>
</body>
</html>
