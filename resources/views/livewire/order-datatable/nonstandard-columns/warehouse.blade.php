
@if($data['warehouse']['symbol'])
    {{ $data['warehouse']['symbol'] ? '<a class="warehouse-symbol" href="/admin/warehouses/' + $data['warehouse']['symbol'] + '/editBySymbol">' + $data['warehouse']['symbol'] + '</a>' : '' }}
@endif
