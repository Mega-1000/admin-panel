<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <style>
            table {
                border-collapse: collapse;
                border: 2px solid black;
                max-width: 800px;
            }

            td {
                border-left: 1px solid black;
                border-right: 1px solid black;
                border-top: 2px solid black;
                border-bottom: 2px solid black;
                padding: 5px;
                min-width: 20px;
                min-height: 10px;
                text-align: center;
            }

            td.light {
                background-color: #EEE;
            }

            td.dark {
                background-color: #DDD;
            }

            td.title {
                background-color: orange;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td rowspan="{{ $hasSubcolumns ? 2 : 1 }}">&nbsp;</td>
                @php ($i = 0)
                @foreach ($cols as $col => $subcols)
                    <td colspan='{{ count($subcols) }}'
                        rowspan='{{ count($subcols) == 1 && $hasSubcolumns && array_key_first($subcols) == "" ? 2 : 1 }}'
                        class='{{ $i++ % 2 ? 'dark' : 'light' }}'>
                        {{ $col }}
                    </td>
                @endforeach
            </tr>
            @if ($hasSubcolumns)
                <tr>
                    @php ($i = 0)
                    @foreach ($cols as $col => $subcols)
                        @foreach ($subcols as $subcol => $notUsed)
                            @if ($subcol != "")
                                <td class='{{ $i % 2 ? 'dark' : 'light' }}'>{!! $subcol ?: '&nbsp;' !!}</td>
                            @endif
                        @endforeach
                        @php ($i++)
                    @endforeach
                </tr>
            @endif
            @foreach ($rows as $title => $rowData)
                <tr>
                    <td class='title'>{{ $title }}</td>
                    @php ($i = 0)
                    @foreach ($cols as $col => $subcols)
                        @foreach ($subcols as $subcol => $notUsed)
                            <td class='{{ $i % 2 ? 'dark' : 'light' }}'>{!! $rowData[$col][$subcol]['price'] ?? '-' !!}</td>
                        @endforeach
                        @php ($i++)
                    @endforeach
                </tr>
            @endforeach
        </table>
    </body>
</html>
