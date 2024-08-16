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

<hr>
@if (isset($order['invoices']))
    @foreach ($order['invoices'] as $invoice)
        @if ($invoice['invoice_type'] === 'buy')
            <a target="_blank" href="/storage/invoices/{{ $invoice['invoice_name'] }}" style="margin-top: 5px;">Faktura</a>

            {{ $invoice['ai_analysis'] }}

            <a class="remove__invoices" href="/delete-invoice?id={{ $invoice['id'] }}">Usuń</a>
            <hr>
        @endif
    @endforeach
    <br />
    @php
        if (preg_match('/taskOrder-(\d+)/', $order['id'], $matches)) {
              $id = $matches[1];
          }
    @endphp
@endif

@php
    $order = Order::find($id);
@endphp
Bilans rozliczeń z fabryką:
{{
    $order->getItemsGrossValueForUs() + $order->shipment_price_for_us -
    $order->payments->where('operation_type', 'Wpłata/wypłata bankowa - związana z fakturą zakupową')->sum('amount')
}}

@if(\App\Entities\BuyingInvoice::where('order_id', $id)->first())
    <hr>
    Faktury zakupu:
    <br>
@endif

@foreach(\App\Entities\BuyingInvoice::where('order_id', $id)->get() as $invoice)
    Faktura numer: {{ $invoice->invoice_number }} Warość: {{ $invoice->value }} PLN
    @if($invoice->analized_by_claute)
        <br>

        <a href="{{ $invoice->file_url }}">
            Analiza AI
        </a>


        <br>

        @if(!$invoice->validated_by_nexo)
            <div style="color: red">
                Nie zweryfikowana przez Nexo
            </div>
        @endif
        @if($invoice->validated_by_nexo)
            <div style="color: green">
                Zweryfikowana przez Nexo
            </div>
        @endif
    @else
        <a href="{{ $invoice->file_url }}">
            Orginał
        </a>
    @endif
    <a class="btn btn-danger" href="/admin/delete-buying-invoice/{{ $invoice->id }}">
        Usuń fakturę
    </a>
    <hr>
@endforeach
<hr>

<a href="{{ rtrim(config('app.front_nuxt_url'), '/') }}/magazyn/awizacja/0/0/{{ $id }}/wyslij-fakture" target="_blank">Dodaj</a>
