<?php

namespace App\Helpers;

use App\Entities\Order;

class OrderDepositPaidCalculator
{
    /**
     * Calculate deposit paid order data
     *
     * @param Order $order
     * @return array
     */
    public function calculateDepositPaidOrderData(Order $order): array
    {
        $totalPayments = 0.0;
        $totalDeclaredPayments = 0.0;
        $totalReturns = 0.0;
        $returnedValue = 0.0;
        $knownPayments = 0.0;
        $externalFirmValue = 0.0;
        $settledDeclaredAmounts = [];
        $wtonValue = 0.0;
        $wpfzValue = 0.0;

        foreach ($order->payments as $payment) {
            if ($payment['operation_type'] === 'Wpłata/wypłata bankowa - związana z fakturą zakupową' && $order['customer']['login'] !== 'info@ephpolska.pl') {
                continue;
            }

            if ($payment['operation_type'] === 'wartość towaru oferty niewyjechanej') {
                $wtonValue += $payment['amount'];
                continue;
            }

            if ($payment->deleted_at !== null) {
                continue;
            }

            $this->processPayment($payment, $totalPayments, $totalDeclaredPayments, $totalReturns, $returnedValue, $knownPayments, $externalFirmValue, $settledDeclaredAmounts);
        }

        $balance = $totalPayments - $totalReturns + $totalDeclaredPayments;
        $offerFinanceBalance = OrderBilansCalculator::calculateCBO(Order::find($order['id']));

        return [
            'totalOfPayments' => $totalPayments,
            'totalOfDeclaredPayments' => $totalDeclaredPayments,
            'balance' => $balance,
            'totalOfReturns' => $totalReturns,
            'settledDeclared' => $settledDeclaredAmounts,
            'payments' => $order->payments,
            'returnedValue' => $returnedValue,
            'knownPayments' => $knownPayments,
            'externalFirmValue' => $externalFirmValue,
            'offerFinanceBalance' => $offerFinanceBalance,
            'wtonValue' => $wtonValue,
            'wpfzValue' => $wpfzValue,
        ];
    }

    private function processPayment(
        $payment,
        float &$totalPayments,
        float &$totalDeclaredPayments,
        float &$totalReturns,
        float &$returnedValue,
        float &$knownPayments,
        float &$externalFirmValue,
        array &$settledDeclaredAmounts
    ): void
    {
        $parsedAmount = floatval($payment->amount);
        $parsedDeclaredAmount = floatval($payment->declared_sum);

        switch ($payment->operation_type) {
            case "Zwrot towaru":
                $returnedValue += $parsedAmount;
                break;
            case '':
                $knownPayments += $parsedAmount;
                break;
            case 'Wartość pobrania przez firmę zewnętrzną':
                $externalFirmValue += $parsedDeclaredAmount;
                break;
            default:
                $this->updateTotals($payment, $parsedAmount, $parsedDeclaredAmount, $totalPayments, $totalDeclaredPayments, $totalReturns, $settledDeclaredAmounts);
                break;
        }
    }

    private function updateTotals(
        $payment,
        float $parsedAmount,
        float $parsedDeclaredAmount,
        float &$totalPayments,
        float &$totalDeclaredPayments,
        float &$totalReturns,
        array &$settledDeclaredAmounts
    ): void
    {
        if ($parsedAmount < 0 && $payment->operation_type !== "Zwrot towaru") {
            $totalReturns -= $parsedAmount;
        } elseif ($parsedAmount > 0) {
            $totalPayments += $parsedAmount;
        } elseif ($parsedDeclaredAmount > 0) {
            if ($payment->status !== 'Rozliczona deklarowana') {
                $totalDeclaredPayments += $parsedDeclaredAmount;
            } else {
                $settledDeclaredAmounts[] = $parsedDeclaredAmount;
            }
        }
    }

}
