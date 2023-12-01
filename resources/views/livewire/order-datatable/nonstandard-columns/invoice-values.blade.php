@php
    $html = "";
    $SBWF = 0;

    foreach ($order['invoices'] as $invoice) {
        $SBWF += floatval($invoice['value']);

        $html .= "
            " . round($invoice['value'] * 100) / 100 . " -
            " . $invoice['invoice_number'] . " -
            " . $invoice['issue_date'] . " -
            <a class='btn btn-danger' id='delete' href='orders/invoice-value-delete/" . $invoice['id'] . "'>Usuń</a>
            <br>
        ";
    }
@endphp

{{ $html }} <br>
SBWF: {{ round($SBWF * 100) / 100 }}  <br>
