<div
    onmouseover="showPhoneInformations('{{ $wholeOrder['created_at'] }}')"
    onmouseout="hidePhoneInformations('{{ $wholeOrder['created_at'] }}')"
>
    <a class="btn btn-primary" href="?{{ $column['label'] }}={{ $data }}">
        NO
    </a>

    <a class="btn btn-success" href="?{{ $column['label'] }}={{ $data }}" target="__blank">
        NOF
    </a>

    <br>

    <div class="tooltip-phone-info" id="tooltip-phone-info-{{ $wholeOrder['created_at'] }}" style="display: none">
        @php
            $phone = $wholeOrder['client_phone'];
            $email = $wholeOrder['client_email'];

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

            if ($data) {
                $html .= '<button class="btn btn-default btn-xs" onclick="filterByPhone(\'' . $data . '\')">F</button>';
                $html .= '<button class="btn btn-default btn-xs" onclick="clearAndfilterByPhone(\'' . $data . '\')">OF</button>';
            }

            if ($data && strpos($data, '48') === 0) {
                $data = substr($data, 2);
            }

            $html .= "<a style='width:100%' target='_blank' href='/admin/orders?nof=" . $data . "' class='btn btn-success'>NOF</a>";
            $html .= '<p data-toggle="tooltip" data-html="true" title="' . $tooltip_title . '">' . $phone . '</p>';

            echo $html;
        @endphp
    </div>
</div>
