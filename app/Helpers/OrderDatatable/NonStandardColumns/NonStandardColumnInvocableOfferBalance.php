<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

use App\Repositories\OrderPackageRealCostsForCompany;

class NonStandardColumnInvocableOfferBalance extends AbstractNonStandardColumnInvocable
{
    protected string $view = 'livewire.order-datatable.nonstandard-columns.offer-balance';

    /**
     * Get data for view.
     *
     * @param array $order
     * @return array
     */
    protected function getData(array $order): array
    {
        $sumOfSelling = $this->calculateSumOfSelling($order);
        $sumOfPurchase = $this->calculateSumOfPurchase($order);
        $RKTBO = OrderPackageRealCostsForCompany::getAllByOrderId($order['id']);

        $PSIK = $this->calculateExpense(
            $order['allegro_general_expenses'],
            [
                'Prowizja od sprzedaży',
                'Jednostkowa opłata transakcyjna',
                'Opłata za udostępnienie metody płatności Allegro Pay'
            ]
        );

        $PSW = $this->calculateExpense(
            $order['allegro_general_expenses'],
            ['Prowizja od sprzedaży oferty wyróżnionej']
        );

        $WAC = $this->calculateExpense(
            $order['allegro_general_expenses'],
            ['Wyrównanie w programie Allegro Ceny']
        );

        $ZP = $this->calculateExpense(
            $order['allegro_general_expenses'],
            ['Zwrot kosztów']
        );

        $Z = $this->calculateZ($order, $sumOfSelling, $sumOfPurchase);
        $BZO = $this->calculateBZO($Z, $RKTBO, $PSIK, $PSW, $WAC, $ZP);

        return [
            'RKTBO' => $RKTBO,
            'PSIK' => $PSIK,
            'PSW' => $PSW,
            'WAC' => $WAC,
            'ZP' => $ZP,
            'Z' => $Z,
            'BZO' => $BZO,
        ];
    }

    private function calculateSumOfSelling(array $order): float
    {
        $sum = 0;

        foreach ($order['items'] as $item) {
            $sum += floatval($item['gross_selling_price_commercial_unit'] ?? 0) * intval($item['quantity'] ?? 0);
        }

        return $sum;
    }

    private function calculateSumOfPurchase(array $order): float
    {
        $sum = 0;

        foreach ($order['items'] as $item) {
            $sum += floatval($item['net_purchase_price_commercial_unit_after_discounts'] ?? 0) * intval($item['quantity'] ?? 0);
        }

        return $sum;
    }

    private function calculateExpense(array $expenses, array $operationDetails): float
    {
        $sum = 0;

        foreach ($expenses as $expense) {
            if (in_array($expense['operation_type'], $operationDetails)) {
                $sum += floatval($expense['debit'] === '0' ? $expense['credit'] : $expense['debit']);
            }
        }

        return $sum;
    }

    private function calculateZ(array $order, float $sumOfSelling, float $sumOfPurchase): float
    {
        return $sumOfSelling + floatval($order['additional_cash_on_delivery_cost'] ?? 0) - ($sumOfPurchase * 1.23) + $order['additional_service_cost'];
    }

    private function calculateBZO(float $Z, $RKTBO, $PSIK, $PSW, $WAC, $ZP): int
    {
        return intval($Z) - intval($RKTBO) + intval($PSIK) - intval($PSW) + intval($WAC) + floatval($ZP);
    }
}
