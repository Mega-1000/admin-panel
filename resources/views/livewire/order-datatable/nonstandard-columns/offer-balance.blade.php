@php
    $RKTBO = App\Repositories\OrderPackageRealCostsForCompany::getAllByOrderId(
        $order['id']
    );
    $PSIK = 0;
    $PSW = 0;
    $WAC = 0;
    $ZP = 0;

    $sumOfSelling = 0;
    $sumOfPurchase = 0;
    $items = $order['items'];

    foreach ($items as $item) {
        $priceSelling = $item['gross_selling_price_commercial_unit'];
        $pricePurchase = $item['net_purchase_price_commercial_unit_after_discounts'];
        $quantity = $item['quantity'];

        $priceSelling = $priceSelling ?? 0;
        $pricePurchase = $pricePurchase ?? 0;
        $quantity = $quantity ?? 0;

        $sumOfSelling += floatval($priceSelling) * intval($quantity);
        $sumOfPurchase += floatval($pricePurchase) * intval($quantity);
    }

    $RKTBOOperationDetails = ['Allegro Paczkomaty Inpost', 'DPD - Kurier opłaty dodatkowe', 'Inpost - dopłaty dodatkowe', 'Przesyłka DPD', 'UPS Operator - opłaty podstawowe'];
    $PSIKOperationDetails = ['Prowizja od sprzedaży', 'Jednostkowa opłata transakcyjna', 'Opłata za udostępnienie metody płatności Allegro Pay'];
    $PSWOperationDetails = ['Prowizja od sprzedaży oferty wyróżnionej'];
    $WACOperationDetails = ['Wyrównanie w programie Allegro Ceny'];
    $ZPOperationDetails = ['Zwrot kosztów'];

    // Compute expenses
    foreach ($order['allegro_general_expenses'] as $expense) {
        $PSIK += in_array($expense['operation_type'], $PSIKOperationDetails) ? floatval($expense['debit'] === '0' ? $expense['credit'] : $expense['debit']) : 0;
        $PSW += in_array($expense['operation_type'], $PSWOperationDetails) ? floatval($expense['debit'] === '0' ? $expense['credit'] : $expense['debit']) : 0;
        $WAC += in_array($expense['operation_type'], $WACOperationDetails) ? floatval($expense['debit'] === '0' ? $expense['credit'] : $expense['debit']) : 0;
        $ZP += in_array($expense['operation_type'], $ZPOperationDetails) ? floatval($expense['debit'] === '0' ? $expense['credit'] : $expense['debit']) : 0;
    }

    // Calculate remaining values
    $Z = ($sumOfSelling + floatval($order['additional_cash_on_delivery_cost'] ?? 0) - ($sumOfPurchase * 1.23));
    $BZO = (intval($Z) - intval($RKTBO) + intval($PSIK) - intval($PSW) + intval($WAC) + floatval($ZP));
@endphp

<p><span title="Zysk">Z: {{ round($Z, 2) }}</p>
<p>RKTBO: {{ round($RKTBO, 2) }}</p>
<p>BT: {{ round($order['shipment_price_for_client'] - $RKTBO, 2) }}</p>
<p>PSIK: {{ round($PSIK, 2) }}</p>
<p>PSW: {{ round($PSW, 2) }}</p>
<p>WAC: {{ round($WAC, 2) }}</p>
<p>ZP: {{ round($ZP, 2) }}</p>
<p>BZO: {{ round($BZO, 2) }}</p>
<p><a href="/admin/allegro-billing?order-id={{ $order['id'] }}" class="btn btn-primary" target="_blank">pokaz na liście</a></p>
