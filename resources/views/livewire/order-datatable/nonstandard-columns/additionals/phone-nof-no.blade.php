<div
    onmouseover="showPhoneInformations('{{ $wholeOrder['created_at'] }}')"
    onmouseout="hidePhoneInformations('{{ $wholeOrder['created_at'] }}')"
>
    Ten użytkownik ma {{ Order::where('customer_id', $wholeOrder['customer']['id'])->count() }} zapytań na swoim koncie.
    <a class="btn btn-primary" href="?{{ $column['label'] }}={{ $data }}">
        NO
    </a>

    <a class="btn btn-success" href="?{{ $column['label'] }}={{ $data }}" target="__blank">
        NOF
    </a>

    <br>

    <div class="tooltip-phone-info" id="tooltip-phone-info-{{ $wholeOrder['created_at'] }}" style="display: none">
        @php
            $phone = $data;
            $email = $wholeOrder['customer']['login'];

            if ($email === null) {
                $email = 'Brak adresu email.';
            }

            $tooltip_title = $email;
            $tooltip_title .= '&#013;';
            $tooltip_title .= '&#013;' . 'Dane do wysylki:';

            foreach ($wholeOrder['addresses'] as $item) {
                if ($item['type'] == 'DELIVERY_ADDRESS') {
                    foreach ($item as $index => $value) {
                        if (!in_array($index, ['type', 'created_at', 'updated_at'])) {
                            $tooltip_title .= $value === null ? ' Brak' : ' ' . $value . ',';
                        }
                    }
                }
            }

            $tooltip_title .= '&#013;' . 'Dane do faktury:';

            foreach ($wholeOrder['addresses'] as $item) {
                if ($item['type'] == 'INVOICE_ADDRESS') {
                    foreach ($item as $index => $value) {
                        if (!in_array($index, ['type', 'created_at', 'updated_at'])) {
                            $tooltip_title .= $value === null ? ' Brak,' : ' ' . $value . ',';
                        }
                    }
                }
            }

            $tooltip_title .= '&#013;';
            $html = '';

            echo $tooltip_title;
        @endphp
    </div>
</div>
