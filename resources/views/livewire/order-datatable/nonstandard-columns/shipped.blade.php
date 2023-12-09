@php
    $data = $order['packages'];
    $cancelled = 0;
    $value = collect($data)->firstWhere('status', 'SENDING');
@endphp
@php
    $html = '';
    foreach ($data as $key => $value) {
        $isProblem = abs(($value['real_cost_for_company'] ?? 0) - ($value['cost_for_company'] ?? 0)) > 2;
        if ($isProblem) {
            $html .= '<div style="border: solid red 4px" >';
        }
        if ($value['status'] === 'SENDING' || $value['status'] === 'DELIVERED') {
            $html .= '<div style="display: flex; align-items: center; flex-direction: column;" > ' .
                '<div style="display: flex; align-items: center;">' .
                '<p style="margin: 8px 0 0 0;">' . $order['id'] . '/' . $value['number'] . '</p>';
            $name = $value['container_type'];
            if ($value['symbol']) {
                $name = $value['symbol'];
            }
            $html .= '<p style="margin: 8px 8px 0 8px;">' . $name . '</p> </div> ';
            if ($value['delivery_cost_balance'] !== 0) {
                $color = '';
                if ($value['delivery_cost_balance'] >= 0) {
                    $color = 'green';
                } else if ($value['delivery_cost_balance'] < 0) {
                    $color = 'red';
                }
                $html .= '<p style="color:' . $color . '">Bilans: ' . $value['delivery_cost_balance'] . '</p>';
            }
            if ($value['letter_number'] === null) {
                $html .= '<a href="javascript:void()"><p>Brak listu przewozowego</p></a>';
            } else {
                $color = '';
                switch ($value['status']) {
                    case 'DELIVERED':
                        $color = '#87D11B';
                        break;
                    case 'SENDING':
                        $color = '#4DCFFF';
                        break;
                    case 'WAITING_FOR_SENDING':
                        $color = '#5537f0';
                        break;
                }
                if ($value['service_courier_name'] === 'INPOST' || $value['service_courier_name'] === 'ALLEGRO-INPOST') {
                    $html .= "<a target=\"_blank\" href=\"/admin/orders/packages/{$value['id']}/sticker\"><p>{$value['letter_number']}</p></a>";
                    $html .= '<div>';
                    if ($value['cash_on_delivery'] !== null && $value['cash_on_delivery'] > 0) {
                        $html .= '<span>' . $value['cash_on_delivery'] . ' zł</span>';
                    }
                    $html .= '<a target="_blank" style="color: green; font-weight: bold; color: #FFFFFF; display: inline-block; margin-top: 5px; margin-left: 5px; padding: 5px; background-color:' . $color . '" href="https://inpost.pl/sledzenie-przesylek?number=' . $value['letter_number'] . '"<i class="fas fa-shipping-fast"></i></a>';
                    $html .= '</div>';
                } else if ($value['delivery_courier_name'] === 'DPD') {
                    $html .= '<p style="margin-bottom: 0px;">' . $value['sending_number'] . '</p>';
                    $html .= "<a target=\"_blank\" href=\"/admin/orders/packages/{$value['id']}/sticker\"><p>{$value['letter_number']}</p></a>";
                    $html .= '<div>';
                    if ($value['cash_on_delivery'] !== null && $value['cash_on_delivery'] > 0) {
                        $html .= '<span>' . $value['cash_on_delivery'] . ' zł</span>';
                    }
                    $html .= '<a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px; background-color:' . $color . '" href="https://tracktrace.dpd.com.pl/parcelDetails?typ=1&p1=' . $value['letter_number'] . '"><i class="fas fa-shipping-fast"></i></a>';
                    $html .= '</div>';
                } else if ($value['delivery_courier_name'] === 'POCZTEX') {
                    $html .= "<a target=\"_blank\" href=\"/admin/orders/packages/{$value['id']}/sticker\"><p>{$value['letter_number']}</p></a>";
                    $html .= '<div>';
                    if ($value['cash_on_delivery'] !== null && $value['cash_on_delivery'] > 0) {
                        $html .= '<span>' . $value['cash_on_delivery'] . ' zł</span>';
                    }
                    $html .= '<a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; margin-top: 5px;padding: 5px;margin-left: 5px; background-color:' . $color . '" href="http://www.pocztex.pl/sledzenie-przesylek/?numer=' . $value['letter_number'] . '"><i class="fas fa-shipping-fast"></i></a>';
                    $html .= '</div>';
                } else if ($value['delivery_courier_name'] === 'JAS') {
                    $html .= "<a target=\"_blank\" href=\"/admin/orders/packages/{$value['id']}/sticker\"><p>{$value['letter_number']}</p></a>";
                    if ($value['cash_on_delivery'] !== null && $value['cash_on_delivery'] > 0) {
                        $html .= '<span>' . $value['cash_on_delivery'] . ' zł</span>';
                    }

                    $html .= '<a target="_blank" href="/storage/jas/labels/label' . $value['sending_number'] . '.pdf"><p>' . $value['letter_number'] . '</p></a>';
                } else if ($value['delivery_courier_name'] === 'GIELDA') {
                    $html .= "<a target=\"_blank\" href=\"/admin/orders/packages/{$value['id']}/sticker\"><p>{$value['letter_number']}</p></a>";
                } else if ($value['delivery_courier_name'] === 'ODBIOR_OSOBISTY') {
                    $html .= "<a target=\"_blank\" href=\"/admin/orders/packages/{$value['id']}/sticker\"><p>{$value['letter_number']}</p></a>";
                } else if ($value['delivery_courier_name'] === 'GLS') {
                    $html .= "<a target=\"_blank\" href=\"/admin/orders/packages/{$value['id']}/sticker\"><p>{$value['letter_number']}";
                    $html .= $value['letter_number'] ? $value['letter_number'] : 'wygeneruj naklejkę';
                    $html .= '<div>';
                    if ($value['cash_on_delivery'] !== null && $value['cash_on_delivery'] > 0) {
                        $html .= '<span>' . $value['cash_on_delivery'] . ' zł</span>';
                    }
                    $html .= '<a target="_blank" style="color: green; font-weight: bold;color: #FFFFFF; display: inline-block; padding: 5px; margin-top: 5px;margin-left: 5px; background-color:' . $color . '" href="https://gls-group.eu/PL/pl/sledzenie-paczek?match=' . $value['letter_number'] . '"><i class="fas fa-shipping-fast"></i></a>';
                    $html .= '</p></a>';
                    $html .= '</div>';
                } else if ($value['delivery_courier_name'] === 'DB') {
                    $html .= "<a target=\"_blank\" href=\"/storage/db_schenker/protocols/protocol{$value['sending_number']}.pdf\"><p>LP: {$value['sending_number']}</p></a>";
                    $html .= "<a target=\"_blank\" href=\"/admin/orders/packages/{$value['id']}/sticker\"><p>KP: {$value['letter_number']}</p></a>";
                }
            }
            $html .= "<button class=\"btn btn-primary\" onclick=\"showPackageCostModal('{$value['id']}', '{$value['chosen_data_template']}', '{$value['cost_for_client']}', '{$value['cost_for_company']}')\">Zmień</button>";
            $html .= '</div>';
        } else if ($value['delivery_courier_name'] === 'DB' && $value['status'] !== 'NEW') {
            $html .= "<a target=\"_blank\" href=\"/storage/db_schenker/protocols/protocol{$value['sending_number']}.pdf\"><p>LP: {$value['sending_number']}</p></a>";
            $html .= "<a target=\"_blank\" href=\"/admin/orders/packages/{$value['id']}/sticker\"><p>KP: {$value['letter_number']}</p></a>";
            $html .= '</div>';
        }
        if ($isProblem) {
            $html .= '</div>';
        }
    }
    echo $html;
@endphp

{{ $order['packages'][0]['real_cost_for_company'] }}

