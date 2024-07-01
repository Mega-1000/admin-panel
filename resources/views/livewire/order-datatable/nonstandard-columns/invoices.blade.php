@php
    $sumOfPurchase = 0;
    $items = $order['items'];

    foreach ($items as $item) {
        $pricePurchase = $item['net_purchase_price_commercial_unit_after_discounts'] ?? 0;
        $quantity = $item['quantity'] ?? 0;
        $sumOfPurchase += floatval($pricePurchase) * intval($quantity);
    }

    $totalItemsCost = $sumOfPurchase * 1.23;
    $transportCost = 0;
@endphp

wartość towaru: <br />
{{ number_format($totalItemsCost, 2) }}<br/>

@if ($order['shipment_price_for_us'])
    Koszt tran.: <br/>
    {{ $order['shipment_price_for_us'] }}<br />
    @php $transportCost = floatval($order['shipment_price_for_us']); @endphp
@endif

@php $totalCost = $totalItemsCost + $transportCost; @endphp
Wartość towaru z transportem: <br /><b>{{ number_format($totalCost, 2) }}</b>

@if (isset($order['invoices']))
    @foreach ($order['invoices'] as $invoice)
        @if ($invoice['invoice_type'] === 'buy')
            <a target="_blank" href="/storage/invoices/{{ $invoice['invoice_name'] }}" style="margin-top: 5px;">Faktura</a>

            @if ($invoice['is_visible_for_client'])
                <p class="invoice__visible">Widoczna</p>
            @else
                <p class="invoice__invisible">Niewidoczna</p>
            @endif

            <a href="#" class="change__invoice--visibility" onclick="changeInvoiceVisibility({{ $invoice['id'] }})">Zmieńwidoczność</a>

            <hr>
        @endif
    @endforeach
    <br />
    @php
        if (preg_match('/taskOrder-(\d+)/', $order['id'], $matches)) {
              $id = $matches[1];
          }
    @endphp

    <div class="remove__invoices" onclick="getInvoicesLists({{ $id }})">Usuń</div>
@endif

@php
    if (preg_match('/taskOrder-(\d+)/', $order['id'], $matches)) {
          $id = $matches[1];
      }
@endphp

<a href="{{ rtrim(config('app.front_nuxt_url'), '/') }}/magazyn/awizacja/0/0/{{ $id }}/wyslij-fakture">Dodaj</a>
