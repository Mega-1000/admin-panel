<!--                             let totalOfPayments = 0;
                            let totalOfDeclaredPayments = 0;
                            let bilans = 0;
                            let totalOfReturns = 0;
                            let text = '';
                            const settledDeclared = [];
                            const payments = row['payments'];
                            let returnedValue = 0;
                            let kwonPayments = 0;
                            let WPFZ = 0;

                            payments.forEach(payment => {
                                const {amount, declared_sum, status} = payment;
                                const parsedAmount = parseFloat(amount);
                                const parsedDeclaredAmount = parseFloat(declared_sum);

                                if (payment.operation_type === 'Wpłata/wypłata bankowa - związana z fakturą zakupową' && row.login !== 'info@ephpolska.pl') {
                                    return;
                                }

                                if (payment.operation_type === "Zwrot towaru") {
                                    returnedValue += parsedAmount;
                                }

                                if (payment.deleted_at !== null) {
                                    return;
                                }

                                if (payment.operation_type === '') {
                                    kwonPayments += parsedAmount;
                                }

                                if (payment.operation_type === 'Wartość pobrania przez firmę zewnętrzną') {
                                    WPFZ += parsedDeclaredAmount;
                                    return;
                                }

                                if (parsedAmount < 0 && payment.operation_type !== "Zwrot towaru") {
                                    totalOfReturns -= parsedAmount ?? parsedDeclaredAmount;
                                } else if (parsedAmount && parsedAmount > 0 && payment.operation_type !== '') {
                                    totalOfPayments += parsedAmount;
                                } else if (!parsedAmount && parsedDeclaredAmount > 0) {
                                    totalOfDeclaredPayments += status === 'Rozliczona deklarowana' ? 0 : parsedDeclaredAmount;

                                    if (status === 'Rozliczona deklarowana') {
                                        settledDeclared.push(parsedDeclaredAmount);
                                    }
                                }
                            });

                            bilans = totalOfPayments - totalOfReturns + totalOfDeclaredPayments;
                            let offerFinanceBilans = row['values_data']['sum_of_gross_values'] - bilans + returnedValue - WPFZ - kwonPayments;

                            text += `<p> Z: ${Math.round(totalOfPayments * 100) / 100} </p>`;
                            text += `<p> ZW: ${Math.round(totalOfReturns * 100) / 100} </p>`;
                            text += `<p> D: ${Math.round(totalOfDeclaredPayments * 100) / 100} </p>`;
                            text += `<p> ZT: ${Math.round(returnedValue * 100) / 100} </p>`;
                            text += `<p> WTON: ${Math.round(kwonPayments * 100) / 100} </p>`;
                            text += `<p> BIF: ${Math.round(bilans * 100) / 100} </p>`;
                            text += `<p> CBO: ${Math.round(offerFinanceBilans * 100) / 100} </p>`;
                            text += `<p> WPFZ: ${Math.round(WPFZ * 100) / 100} </p>`;

                            settledDeclared.forEach((amount) => {
                                text += `<p> RD: ${amount} </p>`;
                            });

                            return text;
                        }
                    },
-->
@php
$totalOfDeclaredPayments = 0;
$bilans = 0;
$totalOfReturns = 0;
$text = '';
$settledDeclared = [];
$payments = $order['payments'];
$returnedValue = 0;
$kwonPayments = 0;
$WPFZ = 0;
$totalOfPayments = 0;

foreach ($payments as $payment) {
    $amount = $payment['amount'];
    $declared_sum = $payment['declared_sum'];
    $status = $payment['status'];
    $parsedAmount = floatval($amount);
    $parsedDeclaredAmount = floatval($declared_sum);

    if ($payment['operation_type'] === 'Wpłata/wypłata bankowa - związana z fakturą zakupową') {
        continue;
    }

    if ($payment['operation_type'] === "Zwrot towaru") {
        $returnedValue += $parsedAmount;
    }

    if ($payment['deleted_at'] !== null) {
        continue;
    }

//    if ($payment['operation_type'] === App\Enums\OrderPaymentsEnum::) {
//        $kwonPayments += $parsedAmount;
//    }

    if ($payment['operation_type'] === 'Wartość pobrania przez firmę zewnętrzną') {
        $WPFZ += $parsedDeclaredAmount;
        continue;
    }

    if ($parsedAmount < 0 && $payment['operation_type'] !== "Zwrot towaru") {
        $totalOfReturns -= $parsedAmount ?? $parsedDeclaredAmount;
    } else if ($parsedAmount && $parsedAmount > 0 && $payment['operation_type'] !== App\Enums\OrderPaymentsEnum::KWON_STATUS) {
        $totalOfPayments += $parsedAmount;
    } else if (!$parsedAmount && $parsedDeclaredAmount > 0) {
        $totalOfDeclaredPayments += $status === 'Rozliczona deklarowana' ? 0 : $parsedDeclaredAmount;

        if ($status === 'Rozliczona deklarowana') {
            $settledDeclared[] = $parsedDeclaredAmount;
        }
    }

}
@endphp
Z: {{ round($totalOfPayments, 2) }} <br>
ZW: {{ round($totalOfReturns, 2) }} <br>
D: {{ round($totalOfDeclaredPayments, 2) }} <br>
ZT: {{ round($returnedValue, 2) }} <br>
WTON: {{ round($kwonPayments, 2) }} <br>
BIF: {{ round($bilans, 2) }} <br>
WPFZ: {{ round($WPFZ, 2) }} <br>

@foreach($settledDeclared as $amount)
    RD: {{ $amount }} <br>
@endforeach

