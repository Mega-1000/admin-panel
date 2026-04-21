<?php

namespace App\Services;

use App\Entities\OrderPackageRealCostForCompany;
use Closure;

class GenerateRealCostsForCompanyReportService
{
    /**
     * Generate real costs for company report
     *
     * @return Closure
     */
    public function generate(): Closure
    {
        return function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['numer faktury', 'wartość']);

            $invoiceTotals = OrderPackageRealCostForCompany::selectRaw('invoice_num, SUM(cost) as total_cost')
                ->groupBy('invoice_num')
                ->get()
                ->toArray();

            foreach ($invoiceTotals as $invoice) {
                fputcsv($file, [
                    $invoice['invoice_num'],
                    $invoice['total_cost'],
                ]);
            }

            fclose($file);
        };
    }


}
