<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <style>
            table {
                border-collapse: collapse;
                width: 780px;
                background-color: #6ec6ff;
                color: #0069c0;
            }

            * {
                box-sizing: border-box;
            }

            td {
                padding: 20px;
                border: 2px dotted #333;
                width: 350px;
            }

            .image {
                float: left;
                width: 150px;
                min-height: 100px;
            }

            img {
                width: 150px;
            }

            .right {
                float: left;
                padding-left: 10px;
                width: 190px;
            }

            .info {
                font-size: 24px;
                text-align: center;
            }

            .title {
                font-size: 20px;
            }

            .price {
                padding-top: 20px;
                font-size: 22px;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <table cellpadding="0" cellspacing="0">
            <tr><td colspan="2" class="info">Na innych naszych aukcjach można zakupić poniższe towary:</td></tr>
            @php ($i = 0)
            @foreach ($rows as $row => $rowCols)
                @foreach ($rowCols as $subcols)
                    @foreach ($subcols as $data)
                        @if (!file_exists(public_path($data['image'])))
                            @continue
                        @endif
                        {!! $i % 2 == 0 ? '<tr>' : '' !!}
                            <td>
                                <div class="image">
                                    <img src="{{ env('APP_URL') }}{{ $data['image'] }}">
                                </div>
                                <div class='right'>
                                    <div class='title'>{{ $data['name'] }}</div>
                                    <div class='price'>{{ $data['price'] }} zł</div>
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
