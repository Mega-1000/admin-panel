<?php

namespace App\Helpers;

use App\Entities\Order;

class OrderBilansCalculator
{
    public static function calculateCBO(Order $order): float
    {
        $totalOfPayments = 0;
        $totalOfDeclaredPayments = 0;
        $bilans = 0;
        $totalOfReturns = 0;
        $settledDeclared = [];
        $returnedValue = 0;
        $kwonPayments = 0;
        $WPFZ = 0;

        $payments = $order->payments; // Assuming payments is a property of the Order class.

        foreach ($payments as $payment) {
            $amount = $payment->amount;
            $declaredSum = $payment->declared_sum;
            $status = $payment->status;

            $parsedAmount = floatval($amount);
            $parsedDeclaredAmount = floatval($declaredSum);

            if ($payment->operation_type === 'Wpłata/wypłata bankowa - związana z fakturą zakupową' && $order->login !== 'info@ephpolska.pl') {
                continue;
            }

            if ($payment->operation_type === "Zwrot towaru") {
                $returnedValue += $parsedAmount;
            }

            if ($payment->deleted_at !== null) {
                continue;
            }

            if ($payment->operation_type === 'wartość towaru oferty niewyjechanej') { // Replace with the actual value
                $kwonPayments += $parsedAmount;
            }

            if ($payment->operation_type === 'Wartość pobrania przez firmę zewnętrzną') {
                $WPFZ += $parsedDeclaredAmount;
                continue;
            }

            if ($parsedAmount < 0 && $payment->operation_type !== "Zwrot towaru") {
                $totalOfReturns -= $parsedAmount ?? $parsedDeclaredAmount;
            } elseif ($parsedAmount > 0 && $payment->operation_type !== 'KWON_STATUS') { // Replace with the actual value
                $totalOfPayments += $parsedAmount;
            } elseif ($parsedAmount == 0 && $parsedDeclaredAmount > 0) {
                $totalOfDeclaredPayments += $status === 'Rozliczona deklarowana' ? 0 : $parsedDeclaredAmount;

                if ($status === 'Rozliczona deklarowana') {
                    $settledDeclared[] = $parsedDeclaredAmount;
                }
            }

            $bilans += $parsedAmount;
        }



        $totalOfDeclaredPayments = $order->payments()->where('operation_type', null)->sum('declared_sum');


        return $order->getSumOfGrossValues() - $bilans - $returnedValue - $WPFZ - $kwonPayments + $totalOfDeclaredPayments;
    }

}
