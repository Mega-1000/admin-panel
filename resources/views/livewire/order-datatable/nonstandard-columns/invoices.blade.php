@if (isset($order['invoices']))
    @foreach ($order['invoices'] as $invoice)
        @if ($invoice['invoice_type'] === 'buy')
            <a target="_blank" href="/storage/invoices/{{ $invoice['invoice_name'] }}" style="margin-top: 5px;">Faktura</a>

            @if ($invoice['is_visible_for_client'])
                <p class="invoice__visible">Widoczna</p>
            @else
                <p class="invoice__invisible">Niewidoczna</p>
            @endif

            <a href="#" class="change__invoice--visibility" onclick="changeInvoiceVisibility({{ $invoice['id'] }})">Zmień widoczność</a>
        @endif
    @endforeach
    <br />
    <a href="#" class="remove__invoices" onclick="getInvoicesList({{ $order['id'] }})">Usuń</a>
@endif

<a href="{{ rtrim(config('app.front_nuxt_url'), '/') }}/magazyn/awizacja/0/0/{{ $order['id'] }}/wyslij-fakture">Dodaj</a>
