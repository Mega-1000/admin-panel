@php
    $totalOfPayments = 0;
    $totalOfDeclaredPayments = 0;
    $bilans = 0;
    $totalOfReturns = 0;
    $text = '';
    $settledDeclared = [];
    $payments = $order['payments'];
    $returnedValue = 0;
    $kwonPayments = 0;
    $WPFZ = 0;

    foreach ($payments as $payment) {
        $amount = $payment['amount'];
        $declared_sum = $payment['declared_sum'];
        $status = $payment['status'];

        $parsedAmount = floatval($amount);
        $parsedDeclaredAmount = floatval($declared_sum);

//        if ($payment['operation_type'] === 'Wpłata/wypłata bankowa - związana z fakturą zakupową' && $row['login'] !== 'info@ephpolska.pl') {
//            continue;
//        }

        if ($payment['operation_type'] === "Zwrot towaru") {
            $returnedValue += $parsedAmount;
        }

        if ($payment['deleted_at'] !== null) {
            continue;
        }

        if ($payment['operation_type'] === '') {
            $kwonPayments += $parsedAmount;
        }

        if ($payment['operation_type'] === 'Wartość pobrania przez firmę zewnętrzną') {
            $WPFZ += $parsedDeclaredAmount;
            continue;
        }

        if ($parsedAmount < 0 && $payment['operation_type'] !== "Zwrot towaru") {
            $totalOfReturns -= $parsedAmount ?? $parsedDeclaredAmount;
        } elseif ($parsedAmount && $parsedAmount > 0 && $payment['operation_type'] !== '') {
            $totalOfPayments += $parsedAmount;
        } elseif (!$parsedAmount && $parsedDeclaredAmount > 0) {
            $totalOfDeclaredPayments += $status === 'Rozliczona deklarowana' ? 0 : $parsedDeclaredAmount;

            if ($status === 'Rozliczona deklarowana') {
                $settledDeclared[] = $parsedDeclaredAmount;
            }
        }
    }

    $bilans = $totalOfPayments - $totalOfReturns + $totalOfDeclaredPayments;
    \App\Helpers\OrderBilansCalculator::calculateCBO(Order::find($order['id']));
@endphp

<p> Z: {{ round($totalOfPayments, 2) }} </p>
<p> ZW: {{ round($totalOfReturns, 2) }} </p>
<p> D: {{ round($totalOfDeclaredPayments, 2) }} </p>
<p> ZT: {{ round($returnedValue, 2) }} </p>
<p> WTON: {{ round($kwonPayments, 2) }} </p>
<p> BIF: {{ round($bilans, 2) }} </p>
<p> CBO: {{ round($offerFinanceBilans, 2) }} </p>
<p> WPFZ: {{ round($WPFZ, 2) }} </p>

@foreach ($settledDeclared as $amount)
    <p> RD: {{ $amount }} </p>
@endforeach
